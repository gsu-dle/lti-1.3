<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use JsonSerializable;

class KeyPair implements JsonSerializable
{
    public readonly string $kid;
    public readonly string $privateKey;
    public readonly string $publicKey;
    public readonly string $kty;
    public readonly string $use;
    public readonly string $n;
    public readonly string $e;
    public readonly int $exp;


    /**
     * @param string $privateKey
     * @param string $publicKey
     * @param string $kty
     * @param string $use
     * @param string $n
     * @param string $e
     * @param int $exp
     */
    public function __construct(
        string $privateKey,
        string $publicKey,
        string $kty,
        string $use,
        string $n,
        string $e,
        int $exp
    ) {
        $this->kid = uniqid('', true);
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->kty = $kty;
        $this->use = $use;
        $this->n = rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($n)), '=');
        $this->e = rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($e)), '=');
        $this->exp = $exp;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'kid' => $this->kid,
            'kty' => $this->kty,
            'use' => $this->use,
            'n' => $this->n,
            'e' => $this->e,
            'exp' => $this->exp
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
        return get_object_vars($this);
    }


    /**
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->kid        = strval($data['kid'] ?? uniqid('', true));
        $this->privateKey = strval($data['privateKey'] ?? '');
        $this->publicKey  = strval($data['publicKey'] ?? '');
        $this->kty        = strval($data['kty'] ?? '');
        $this->use        = strval($data['use'] ?? '');
        $this->n          = strval($data['n'] ?? '');
        $this->e          = strval($data['e'] ?? '');
        $this->exp        = intval($data['exp'] ?? 0);
    }
}
