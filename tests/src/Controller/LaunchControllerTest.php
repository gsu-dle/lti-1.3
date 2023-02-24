<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Controller;

use GAState\Web\LTI\Action\LaunchAction           as LaunchAction;
use GAState\Web\LTI\Exception\BadRequestException as BadRequestException;
use GAState\Web\LTI\Model\Message                 as Message;
use PHPUnit\Framework\MockObject\MockObject       as MockObject;
use PHPUnit\Framework\TestCase                    as TestCase;
use Psr\Cache\CacheException                      as CacheException;
use Psr\Cache\CacheItemPoolInterface              as Cache;
use Psr\Cache\CacheItemInterface                  as CacheItem;
use Psr\Http\Message\ResponseInterface            as Response;
use Psr\Http\Message\ServerRequestInterface       as Request;
use Psr\Http\Message\UriInterface                 as Uri;
use ReflectionClass                               as ReflectionClass;

final class LaunchControllerTest extends TestCase
{
    private string $baseURI;
    private string $launchPrefix;
    private string $statePrefix;
    private string $noncePrefix;

    /**
     * @var array<string,mixed> $params
     */
    private array $params = [];

    /**
     * @var array<string,mixed> $cookies
     */
    private array $cookies = [];

    private MockObject&LaunchAction $launchAction;
    private MockObject&Cache $appCache;
    private MockObject&CacheItem $appCacheItem;
    private MockObject&Request $request;
    private MockObject&Uri $requestURI;
    private MockObject&Message $message;
    private MockObject&Response $response;


    public function setUp(): void
    {
        $this->baseURI = '/testLogin/';
        $this->launchPrefix = 'testLogin-launch-';
        $this->statePrefix = 'testLogin-state-';
        $this->noncePrefix = 'testLogin-nonce-';

        $this->params = [
            'id_token' => '__id_token__',
            'state'    => "testLogin-state-__state__"
        ];

        $this->cookies = [
            'lti-1.3-testLogin-state-__state__' => 'testLogin-state-__state__'
        ];

        /** @var MockObject&LaunchAction $launchAction */
        $launchAction = $this->launchAction = $this->createMock(LaunchAction::class);
        /** @var MockObject&Cache $appCache */
        $appCache = $this->appCache = $this->createMock(Cache::class);
        /** @var MockObject&CacheItem $appCacheItem */
        $appCacheItem = $this->appCacheItem = $this->createMock(CacheItem::class);
        /** @var MockObject&Request $request */
        $request = $this->request = $this->createMock(Request::class);
        /** @var MockObject&Message $message */
        $message = $this->message = $this->createMock(Message::class);
        /** @var MockObject&Response $response */
        $response = $this->response = $this->createMock(Response::class);
        /** @var MockObject&Uri $requestURI */
        $requestURI = $this->requestURI = $this->createMock(Uri::class);

        $this->request->method('getUri')->willReturn($this->requestURI);
    }


    public function testLaunch(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->request
            ->expects(self::once())
            ->method('getCookieParams')
            ->willReturn($this->cookies);

        $this->requestURI
            ->expects(self::once())
            ->method('getScheme')
            ->willReturn('https');

        (new ReflectionClass(Message::class))
            ->getProperty('targetLinkURI')
            ->setValue($this->message, '__targetLinkURI__');

        (new ReflectionClass(Message::class))
            ->getProperty('launchID')
            ->setValue($this->message, "{$this->launchPrefix}__launch_id__");

        $this->launchAction
            ->expects(self::once())
            ->method('launch')
            ->with(
                self::stringStartsWith($this->launchPrefix),
                self::equalTo('testLogin-state-__state__'),
                self::equalTo('testLogin-nonce-__nonce__'),
                self::equalTo('__id_token__'),
                self::equalTo('testLogin-state-__state__'),
            )
            ->willReturn($this->message);

        $this->appCache
            ->expects(self::once())
            ->method('save')
            ->willReturn(true);

        $this->appCache
            ->expects(self::exactly(2))
            ->method('getItem')
            ->withConsecutive(
                [
                    self::equalTo("lti-1.3-testLogin-state-__state__")
                ],
                [
                    self::stringStartsWith("lti-1.3-{$this->launchPrefix}")
                ]
            )
            ->willReturn($this->appCacheItem);

        $this->appCacheItem
            ->expects(self::once())
            ->method('get')
            ->willReturn('testLogin-nonce-__nonce__');

        $this->appCacheItem
            ->expects(self::once())
            ->method('set')
            ->with(
                self::equalTo($this->message)
            )
            ->willReturnSelf();

        $this->appCacheItem
            ->expects(self::once())
            ->method('expiresAfter')
            ->with(
                self::equalTo(2 * 60 * 60)
            )
            ->willReturnSelf();

        $this->response
            ->expects(self::once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects(self::exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                [
                    self::equalTo('Set-Cookie'),
                    self::callback(function ($cookie): bool {
                        return is_string($cookie)
                            && str_starts_with($cookie, "lti-1.3-launch=")
                            && str_contains($cookie, " Secure; ")
                            && str_contains($cookie, " SameSite=none; ")
                            && str_contains($cookie, " HttpOnly; ")
                            && str_contains($cookie, " Path={$this->baseURI}; ")
                            && str_contains($cookie, " Max-Age=7200");
                    })
                ],
                [
                    self::equalTo('Location'),
                    self::equalTo($this->message->targetLinkURI)
                ]
            )
            ->willReturnOnConsecutiveCalls()
            ->willReturnSelf();

        $this->createLaunchController()->launch($this->request, $this->response);

        self::assertEquals(0, 0);
    }


    public function testMissingIdTokenParam(): void
    {
        unset($this->params['id_token']);

        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing 'id_token' parameter");

        $this->createLaunchController()->launch($this->request, $this->response);

        self::fail();
    }


    public function testStateParam(): void
    {
        unset($this->params['state']);

        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing 'state' parameter");

        $this->createLaunchController()->launch($this->request, $this->response);

        self::fail();
    }


    public function testMissingStateCookie(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing 'state' cookie");

        $this->createLaunchController()->launch($this->request, $this->response);

        self::fail();
    }


    public function testMissingNonceValue(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->request
            ->expects(self::once())
            ->method('getCookieParams')
            ->willReturn($this->cookies);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing 'nonce' value");

        $this->createLaunchController()->launch($this->request, $this->response);

        self::fail();
    }


    public function testCacheException(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->request
            ->expects(self::once())
            ->method('getCookieParams')
            ->willReturn($this->cookies);

        (new ReflectionClass(Message::class))
            ->getProperty('launchID')
            ->setValue($this->message, "{$this->launchPrefix}__launch_id__");

        $this->launchAction
            ->expects(self::once())
            ->method('launch')
            ->willReturn($this->message);

        $this->appCache
            ->expects(self::once())
            ->method('save')
            ->willReturn(false);

        $this->appCache
            ->expects(self::atLeast(1))
            ->method('getItem')
            ->willReturn($this->appCacheItem);

        $this->appCacheItem
            ->expects(self::once())
            ->method('get')
            ->willReturn('testLogin-nonce-__nonce__');

        $this->expectException(CacheException::class);

        $this->createLaunchController()->launch($this->request, $this->response);

        self::fail();
    }


    /**
     * @return LaunchController
     */
    private function createLaunchController(): LaunchController
    {
        return new LaunchController(
            baseURI: $this->baseURI,
            action: $this->launchAction,
            appCache: $this->appCache,
            launchPrefix: $this->launchPrefix,
            statePrefix: $this->statePrefix,
            noncePrefix: $this->noncePrefix
        );
    }
}
