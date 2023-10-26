<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Util;

use GAState\Web\LTI\Util\KeyPairFactoryInterface as KeyPairFactory;
use Psr\Cache\CacheItemPoolInterface             as Cache;

class JWKSFactory implements JWKSFactoryInterface
{
    protected KeyPairFactory $keyPairFactory;
    protected Cache $appCache;
    protected int $jwksExpiresAfter;
    protected int $jwksRegenKeyAt;
    protected string $jwksCacheName;
    protected ?JWKS $jwks = null;


    /**
     * @param KeyPairFactory $keyPairFactory
     * @param Cache $appCache
     * @param int $jwksExpiresAfter
     * @param int $jwksRegenKeyAt
     * @param string $jwksCacheName
     */
    public function __construct(
        KeyPairFactory $keyPairFactory,
        Cache $appCache,
        int $jwksExpiresAfter = 3600,
        int $jwksRegenKeyAt = 360,
        string $jwksCacheName = 'lti-1.3-jwks'
    ) {
        $this->keyPairFactory = $keyPairFactory;
        $this->appCache = $appCache;
        $this->jwksExpiresAfter = $jwksExpiresAfter;
        $this->jwksRegenKeyAt = $jwksRegenKeyAt;
        $this->jwksCacheName = $jwksCacheName;
    }


    public function __destruct()
    {
       
    }


    /**
     * @return JWKS
     */
    public function createJWKS(): JWKS
    {
        if ($this->jwks === null) {
            $cacheItem = $this->appCache->getItem($this->jwksCacheName);
            $jwks = $cacheItem->get();

            if (!$jwks instanceof JWKS) {
                $jwks = new JWKS(
                    keyPairFactory: $this->keyPairFactory,
                    expiresAfter: $this->jwksExpiresAfter,
                    regenKeyAt: $this->jwksRegenKeyAt,
                );
                $this->appCache->save($cacheItem->set($jwks));
            }

            $this->jwks = $jwks;
        }

        return $this->jwks;
    }
}
