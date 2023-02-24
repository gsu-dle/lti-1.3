<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

interface JWKSFactoryInterface
{
    /**
     * @return JWKS
     */
    public function createJWKS(): JWKS;
}
