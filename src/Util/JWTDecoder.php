<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key as FirebaseKey;
use Firebase\JWT\CachedKeySet as FirebaseKeySet;

class JWTDecoder implements JWTDecoderInterface
{
    /** @var array<string,FirebaseKey>|FirebaseKey $keyOrKeyArray */
    private array|FirebaseKey $keyOrKeyArray;


    /**
     * @param array<string,FirebaseKey>|FirebaseKeySet|FirebaseKey|KeyPair|string $keyOrKeyArray
     * @param string $defaultAlg
     */
    public function __construct(
        array|FirebaseKeySet|FirebaseKey|KeyPair|string $keyOrKeyArray,
        string $defaultAlg = 'RS256'
    ) {
        if (is_string($keyOrKeyArray)) {
            $keyOrKeyArray = new FirebaseKey($keyOrKeyArray, $defaultAlg);
        } elseif ($keyOrKeyArray instanceof KeyPair) {
            $keyOrKeyArray = new FirebaseKey($keyOrKeyArray->publicKey, $defaultAlg);
        }

        /** @var array<string,FirebaseKey>|FirebaseKey $keyOrKeyArray */
        $this->keyOrKeyArray = $keyOrKeyArray;
    }


    /**
     * @param string $jwt
     *
     * @return array<string, mixed>
     */
    public function decode(string $jwt): array
    {
        /** @var array<string, mixed> $decodedValues */
        $decodedValues = (array) FirebaseJWT::decode($jwt, $this->keyOrKeyArray);
        return $decodedValues;
    }
}
