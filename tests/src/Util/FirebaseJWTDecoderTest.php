<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use GAState\Web\LTI\Util\JWTDecoder          as FirebaseJWTDecoder;
use GAState\Web\LTI\Util\JWTEncoder          as FirebaseJWTEncoder;
use GAState\Web\LTI\Util\JWTDecoderInterface as JWTDecoder;
use GAState\Web\LTI\Util\JWTEncoderInterface as JWTEncoder;

final class JWTDecoderTest extends JWTDecoderInterfaceTest
{
    private JWTDecoder $jwtDecoder;
    private JWTEncoder $jwtEncoder;


    public function setUp(): void
    {
        $privateKey = strval(file_get_contents(__DIR__ . '/../../keys/lms.key'));
        $publicKey = strval(file_get_contents(__DIR__ . '/../../keys/lms.pub'));

        $this->jwtDecoder = new FirebaseJWTDecoder($publicKey);
        $this->jwtEncoder = new FirebaseJWTEncoder($privateKey);
    }


    /**
     * @return JWTDecoder
     */
    protected function getJWTDecoder(): JWTDecoder
    {
        return $this->jwtDecoder;
    }


    /**
     * @return JWTEncoder
     */
    protected function getJWTEncoder(): JWTEncoder
    {
        return $this->jwtEncoder;
    }
}
