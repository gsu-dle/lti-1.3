<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use Exception                                    as Exception;
use GAState\Web\LTI\Util\KeyPairFactoryInterface as KeyPairFactory;
use JsonSerializable                             as JsonSerializable;

class JWKS implements JsonSerializable
{
    protected int $expiresAfter;
    protected int $regenKeyAt;
    protected KeyPairFactory $keyPairFactory;
    protected ?KeyPair $currentKeyPair = null;
    protected ?KeyPair $newKeyPair = null;

    /**
     * @var array<string,KeyPair> $keyPairs
     */
    protected array $keyPairs = [];


    /**
     * @param KeyPairFactory $keyPairFactory
     * @param int $expiresAfter
     * @param int $regenKeyAt
     */
    public function __construct(
        KeyPairFactory $keyPairFactory,
        int $expiresAfter = 3600,
        int $regenKeyAt = 500
    ) {
        $this->keyPairFactory = $keyPairFactory;
        if ($expiresAfter < 1) {
            throw new Exception(); // TODO: add specific error
        }
        if ($regenKeyAt < 0 || $regenKeyAt >= $expiresAfter) {
            throw new Exception(); // TODO: add specific error
        }
        $this->expiresAfter = $expiresAfter;
        $this->regenKeyAt = $regenKeyAt;

        $this->refresh();
    }


    /**
     * @return self
     */
    public function refresh(): self
    {
        $rightNow = time();
        $closeToExpire = $rightNow - $this->regenKeyAt;

        if ($this->currentKeyPair === null || $rightNow >= $this->currentKeyPair->exp) {
             $this->keyPairs = [];

            if ($this->newKeyPair !== null && $closeToExpire < $this->newKeyPair->exp) {
                $this->currentKeyPair = $this->newKeyPair;
            } else {
                $this->currentKeyPair = $this->keyPairFactory->createKeyPair($rightNow + $this->expiresAfter);
            }
            $this->newKeyPair = null;

            $this->keyPairs[$this->currentKeyPair->kid] = $this->currentKeyPair;
        } elseif ($closeToExpire >= $this->currentKeyPair->exp) {
            if ($this->newKeyPair === null || $closeToExpire >= $this->newKeyPair->exp) {
                $this->newKeyPair = $this->keyPairFactory->createKeyPair($rightNow + $this->expiresAfter);
                $this->keyPairs[$this->newKeyPair->kid] = $this->newKeyPair;
            }
        }

        return $this;
    }


    /**
     * @return array<string,KeyPair>
     */
    public function getKeyPairs(): array
    {
        return $this->refresh()->keyPairs;
    }


    /**
     * @param string $kid
     *
     * @return KeyPair|null
     */
    public function getKeyPair(string $kid): ?KeyPair
    {
        return $this->getKeyPairs()[$kid] ?? null;
    }


    /**
     * @return KeyPair
     */
    public function getAvailableKeyPair(): KeyPair
    {
        $keyPairs = $this->getKeyPairs();
        $keyPair = array_pop($keyPairs);
        if ($keyPair === null) {
            throw new Exception(); // TODO: replace with specific error
        }
        return $keyPair;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'keys' => array_values($this->getKeyPairs())
        ];
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return strval(json_encode($this));
    }


    /**
     * @return array<string,mixed>
     */
    public function __serialize(): array
    {
        return [
            'expiresAfter'   => $this->expiresAfter,
            'regenKeyAt'     => $this->regenKeyAt,
            'keyPairFactory' => $this->keyPairFactory,
            'currentKeyPair' => $this->currentKeyPair,
            'newKeyPair'     => $this->newKeyPair,
        ];
    }


    /**
     * @param array<string,mixed> $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $expiresAfter = $data['expiresAfter'] ?? null;
        if (!is_int($expiresAfter)) {
            throw new Exception();
        }

        $regenKeyAt = $data['regenKeyAt'] ?? null;
        if (!is_int($regenKeyAt)) {
            throw new Exception();
        }

        $keyPairFactory = $data['keyPairFactory'] ?? null;
        if (!$keyPairFactory instanceof KeyPairFactory) {
            throw new Exception();
        }

        $currentKeyPair = $data['currentKeyPair'] ?? null;
        if (!($currentKeyPair instanceof KeyPair || $currentKeyPair === null)) {
            throw new Exception();
        }

        $newKeyPair = $data['newKeyPair'] ?? null;
        if (!($newKeyPair instanceof KeyPair || $newKeyPair === null)) {
            throw new Exception();
        }

        $this->expiresAfter = $expiresAfter;
        $this->regenKeyAt = $regenKeyAt;
        $this->keyPairFactory = $keyPairFactory;
        $this->currentKeyPair = $currentKeyPair;
        $this->newKeyPair = $newKeyPair;

        $this->keyPairs = [];
        if ($this->currentKeyPair !== null) {
            $this->keyPairs[$this->currentKeyPair->kid] = $this->currentKeyPair;
            if ($this->newKeyPair !== null) {
                $this->keyPairs[$this->newKeyPair->kid] = $this->newKeyPair;
            }
        }

        $this->refresh();
    }
}
