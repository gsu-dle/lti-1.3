<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Exception;

use Psr\Cache\CacheException as PsrCacheException;

class CacheException extends LTIException implements PsrCacheException
{
}
