<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use GAState\Web\LTI\Util\JWTDecoderInterface as JWTDecoder;
use GAState\Web\LTI\Util\JWTEncoderInterface as JWTEncoder;
use PHPUnit\Framework\TestCase               as TestCase;
use Throwable                                as Throwable;

abstract class JWTDecoderInterfaceTest extends TestCase
{
    public function testDecode(): void
    {
        $rightNow = time();
        $payload = [
            'foo' => 'bar',
            'iat' => $rightNow,
            'nbf' => $rightNow,
            'exp' => $rightNow + 5
        ];

        $encrypted = $this->getJWTEncoder()->encode($payload);
        $decrypted = $this->getJWTDecoder()->decode($encrypted);

        self::assertEquals(count($payload), count($decrypted));
        foreach ($payload as $name => $value) {
            self::assertEquals($value, $decrypted[$name] ?? null);
        }
    }


    public function testMalformedJWT(): void
    {
        $jwt = 'I am an invalid JWT';
        $this->expectException(Throwable::class);
        $this->getJWTDecoder()->decode($jwt);
        self::fail("Didn't throw exception");
    }


    public function testIssuedAtException(): void
    {
        $jwt = $this->getJWTEncoder()->encode(['iat' => PHP_INT_MAX]);
        $this->expectException(Throwable::class);
        $this->getJWTDecoder()->decode($jwt);
        self::fail("Didn't throw exception");
    }


    public function testNotBeforeException(): void
    {
        $jwt = $this->getJWTEncoder()->encode(['nbf' => PHP_INT_MAX]);
        $this->expectException(Throwable::class);
        $this->getJWTDecoder()->decode($jwt);
        self::fail("Didn't throw exception");
    }


    public function testExpirationTimeException(): void
    {
        $jwt = $this->getJWTEncoder()->encode(['exp' => PHP_INT_MIN]);
        $this->expectException(Throwable::class);
        $this->getJWTDecoder()->decode($jwt);
        self::fail();
    }


    /**
     * @return JWTDecoder
     */
    abstract protected function getJWTDecoder(): JWTDecoder;


    /**
     * @return JWTEncoder
     */
    abstract protected function getJWTEncoder(): JWTEncoder;
}
