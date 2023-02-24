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
     */
    public function __construct(array|FirebaseKeySet|FirebaseKey|KeyPair|string $keyOrKeyArray)
    {
        if ($keyOrKeyArray instanceof FirebaseKeySet) {
            /** @var array<string,FirebaseKey> $keyOrKeyArray */
            $keyOrKeyArray = (array) $keyOrKeyArray;
        }
        if ($keyOrKeyArray instanceof KeyPair) {
            $keyOrKeyArray = $keyOrKeyArray->publicKey;
        }
        $this->keyOrKeyArray = is_string($keyOrKeyArray)
            ? new FirebaseKey($keyOrKeyArray, 'RS256')
            : $keyOrKeyArray;
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
