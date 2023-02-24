<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

interface JWTDecoderInterface
{
    /**
     * @param string $jwt
     *
     * @return array<string, mixed>
     */
    public function decode(string $jwt): array;
}
