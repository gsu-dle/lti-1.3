<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use Exception            as Exception;
use Firebase\JWT\JWT     as FirebaseJWT;
use Firebase\JWT\Key     as FirebaseKey;
use OpenSSLAsymmetricKey as OpenSSLAsymmetricKey;
use OpenSSLCertificate   as OpenSSLCertificate;

class JWTEncoder implements JWTEncoderInterface
{
    private string $key;
    private ?string $alg;
    private ?string $kid;


    /**
     * @param FirebaseKey|KeyPair|string $key
     * @param ?string $alg
     * @param ?string $kid
     */
    public function __construct(
        FirebaseKey|KeyPair|string $key,
        ?string $alg = null,
        ?string $kid = null,
    ) {
        if ($key instanceof FirebaseKey) {
            if ($alg === null) {
                $alg = $key->getAlgorithm();
            }

            $key = $key->getKeyMaterial();

            if ($key instanceof OpenSSLAsymmetricKey) {
                $privateKey = '';
                openssl_pkey_export($key, $privateKey);
                $key = $privateKey;
            } elseif ($key instanceof OpenSSLCertificate) {
                $privateKey = '';
                openssl_x509_export($key, $privateKey);
                $key = $privateKey;
            }
        } elseif ($key instanceof KeyPair) {
            if ($kid === null) {
                $kid = $key->kid;
            }
            $key = $key->privateKey;
        }

        if (!is_string($key)) {
            throw new Exception(); // TODO: add specific error
        }

        $this->kid = $kid;
        $this->key = $key;
        $this->alg = $alg;
    }


    /**
     * @param mixed $payload
     *
     * @return string
     */
    public function encode(mixed $payload): string
    {
        if (is_object($payload)) {
            $payload = get_object_vars($payload);
        }
        if (!is_array($payload)) {
            throw new Exception(); // TODO: throw specific error
        }

        return FirebaseJWT::encode(
            payload: $payload,
            key: $this->key,
            alg: $this->alg ?? 'RS256',
            keyId: $this->kid
        );
    }
}
