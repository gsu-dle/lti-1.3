<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Controller;

use GAState\Web\LTI\Action\LoginAction            as LoginAction;
use GAState\Web\LTI\Exception\BadRequestException as BadRequestException;
use PHPUnit\Framework\MockObject\MockObject       as MockObject;
use PHPUnit\Framework\TestCase                    as TestCase;
use Psr\Cache\CacheException                      as CacheException;
use Psr\Cache\CacheItemPoolInterface              as Cache;
use Psr\Cache\CacheItemInterface                  as CacheItem;
use Psr\Http\Message\ResponseInterface            as Response;
use Psr\Http\Message\ServerRequestInterface       as Request;
use Psr\Http\Message\UriInterface                 as Uri;

final class LoginControllerTest extends TestCase
{
    private string $lmsLoginURL;
    private string $baseURI;
    private string $statePrefix;
    private string $noncePrefix;

    /**
     * @var array<string,mixed> $params
     */
    private array $params = [];

    private MockObject&LoginAction $loginAction;
    private MockObject&Cache $appCache;
    private MockObject&CacheItem $appCacheItem;
    private MockObject&Request $request;
    private MockObject&Uri $requestURI;
    private MockObject&Response $response;


    public function setUp(): void
    {
        $this->lmsLoginURL = 'https://lms/lti/authenticate';
        $this->baseURI = '/testLogin/';
        $this->statePrefix = 'testLogin-state-';
        $this->noncePrefix = 'testLogin-nonce-';

        $this->params = [
            'iss'               => '__iss__',
            'client_id'         => '__client_id__',
            'lti_deployment_id' => '__lti_deployment_id__',
            'login_hint'        => '__login_hint__',
            'lti_message_hint'  => '__lti_message_hint__'
        ];

        /** @var MockObject&LoginAction $loginAction */
        $loginAction = $this->loginAction = $this->createMock(LoginAction::class);
        /** @var MockObject&Cache $appCache */
        $appCache = $this->appCache = $this->createMock(Cache::class);
        /** @var MockObject&CacheItem $appCacheItem */
        $appCacheItem = $this->appCacheItem = $this->createMock(CacheItem::class);
        /** @var MockObject&Request $request */
        $request = $this->request = $this->createMock(Request::class);
        /** @var MockObject&Response $response */
        $response = $this->response = $this->createMock(Response::class);
        /** @var MockObject&Uri $requestURI */
        $requestURI = $this->requestURI = $this->createMock(Uri::class);

        $this->request->method('getUri')->willReturn($this->requestURI);
    }


    public function testLogin(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->requestURI
            ->expects(self::once())
            ->method('getScheme')
            ->willReturn('https');

        $this->loginAction
            ->expects(self::once())
            ->method('login')
            ->with(
                self::stringStartsWith($this->statePrefix),
                self::stringStartsWith($this->noncePrefix),
                self::equalTo('__iss__'),
                self::equalTo('__client_id__'),
                self::equalTo('__lti_deployment_id__'),
                self::equalTo('__login_hint__'),
                self::equalTo('__lti_message_hint__'),
            )
            ->willReturn($this->lmsLoginURL);

        $this->appCache
            ->expects(self::exactly(2))
            ->method('save')
            ->willReturn(true);

        $this->appCache
            ->expects(self::exactly(2))
            ->method('getItem')
            ->withConsecutive(
                [self::stringStartsWith("lti-1_3-{$this->statePrefix}")],
                [self::stringStartsWith("lti-1_3-{$this->noncePrefix}")]
            )
            ->willReturn($this->appCacheItem);

        $this->appCacheItem
            ->expects(self::exactly(2))
            ->method('set')
            ->withConsecutive(
                [self::stringStartsWith($this->noncePrefix)],
                [self::greaterThanOrEqual(time() + 60)]
            )
            ->willReturnSelf();

        $this->appCacheItem
            ->expects(self::exactly(2))
            ->method('expiresAfter')
            ->with(60)
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
                            && str_starts_with($cookie, "lti-1_3-{$this->statePrefix}")
                            && str_contains($cookie, " Secure; ")
                            && str_contains($cookie, " SameSite=none; ")
                            && str_contains($cookie, " HttpOnly; ")
                            && str_contains($cookie, " Path={$this->baseURI}; ")
                            && str_contains($cookie, " Max-Age=60");
                    })
                ],
                [
                    self::equalTo('Location'),
                    self::equalTo($this->lmsLoginURL)
                ]
            )
            ->willReturnOnConsecutiveCalls()
            ->willReturnSelf();

        $this->createLoginController()->login($this->request, $this->response);

        self::assertEquals(0, 0);
    }


    public function testParsedBodyIsNull(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(null);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Unable to read params');

        $this->createLoginController()->login($this->request, $this->response);

        self::fail();
    }


    public function testMissingIss(): void
    {
        $this->testMissingParam('iss');
    }


    public function testMissingClientId(): void
    {
        $this->testMissingParam('client_id');
    }


    public function testMissingDeploymentId(): void
    {
        $this->testMissingParam('lti_deployment_id');
    }


    public function testMissingLoginHint(): void
    {
        $this->testMissingParam('login_hint');
    }


    public function testStateCacheException(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->requestURI
            ->expects(self::once())
            ->method('getScheme')
            ->willReturn('https');

        $this->loginAction
            ->expects(self::once())
            ->method('login')
            ->with(
                self::stringStartsWith($this->statePrefix),
                self::stringStartsWith($this->noncePrefix),
                self::equalTo('__iss__'),
                self::equalTo('__client_id__'),
                self::equalTo('__lti_deployment_id__'),
                self::equalTo('__login_hint__'),
                self::equalTo('__lti_message_hint__'),
            )
            ->willReturn($this->lmsLoginURL);

        $this->appCache
            ->expects(self::once())
            ->method('save')
            ->willReturn(false);

        $this->expectException(CacheException::class);

        $this->createLoginController()->login($this->request, $this->response);

        self::fail();
    }


    public function testNonceCacheException(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->requestURI
            ->expects(self::once())
            ->method('getScheme')
            ->willReturn('https');

        $this->loginAction
            ->expects(self::once())
            ->method('login')
            ->with(
                self::stringStartsWith($this->statePrefix),
                self::stringStartsWith($this->noncePrefix),
                self::equalTo('__iss__'),
                self::equalTo('__client_id__'),
                self::equalTo('__lti_deployment_id__'),
                self::equalTo('__login_hint__'),
                self::equalTo('__lti_message_hint__'),
            )
            ->willReturn($this->lmsLoginURL);

        $this->appCache
            ->expects(self::exactly(2))
            ->method('save')
            ->willReturn(true, false);

        $this->expectException(CacheException::class);

        $this->createLoginController()->login($this->request, $this->response);

        self::fail();
    }


    /**
     * @return LoginController
     */
    private function createLoginController(): LoginController
    {
        return new LoginController(
            baseURI: $this->baseURI,
            action: $this->loginAction,
            appCache: $this->appCache,
            statePrefix: $this->statePrefix,
            noncePrefix: $this->noncePrefix
        );
    }


    /**
     * @param string $name
     *
     * @return void
     */
    private function testMissingParam(string $name): void
    {
        unset($this->params[$name]);

        $this->request
            ->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($this->params);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing '{$name}' parameter");

        $this->createLoginController()->login($this->request, $this->response);

        self::fail();
    }
}
