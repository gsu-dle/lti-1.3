<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

interface JWTEncoderInterface
{
    /**
     * @param mixed $payload
     *
     * @return string
     */
    public function encode(mixed $payload): string;
}
