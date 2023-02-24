<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Middleware;

use GAState\Web\LTI\Model\Message           as Message;
use Psr\Cache\CacheItemPoolInterface        as Cache;
use Psr\Http\Message\ResponseInterface      as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface     as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class LaunchMessageMiddleware implements Middleware
{
    protected readonly Cache $appCache;


    /**
     * @param Cache $appCache
     */
    public function __construct(Cache $appCache)
    {
        $this->appCache = $appCache;
    }


    /**
     * @param Request $request
     * @param RequestHandler $handler
     *
     * @return Response
     */
    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {
        $cookies = $request->getCookieParams();
        $launchID = strval($cookies["lti-1.3-launch"] ?? '');
        $message = $this->appCache->getItem("lti-1.3-{$launchID}")->get();
        if ($message instanceof Message) {
            $request = $request->withAttribute('lti-1.3-launch', $message);
        }

        return $handler->handle($request);
    }
}
