<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

interface KeyPairFactoryInterface
{
    public function createKeyPair(int $expiresOn): KeyPair;
}
