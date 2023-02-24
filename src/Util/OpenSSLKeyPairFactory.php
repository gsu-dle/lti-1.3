<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use Exception            as Exception;
use OpenSSLAsymmetricKey as OpenSSLAsymmetricKey;

class OpenSSLKeyPairFactory implements KeyPairFactoryInterface
{
    public function createKeyPair(int $expiresOn): KeyPair
    {
        $key = openssl_pkey_new([
            'digest_alg'       => 'sha256',
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 4096
        ]);
        if (!$key instanceof OpenSSLAsymmetricKey) {
            throw new Exception(); // TODO: add specific exception
        }
        $privateKey = '';
        openssl_pkey_export($key, $privateKey);
        $details = openssl_pkey_get_details($key);
        $publicKey = strval($details['key'] ?? '');
        $n = $details['rsa']['n'] ?? '';
        $e = $details['rsa']['e'] ?? '';

        return new KeyPair(
            $privateKey,
            $publicKey,
            'RSA',
            'sig',
            $n,
            $e,
            $expiresOn
        );
    }
}
