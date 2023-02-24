<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Controller;

use GAState\Web\LTI\Action\LoginAction            as LoginAction;
use GAState\Web\LTI\Exception\BadRequestException as BadRequestException;
use GAState\Web\LTI\Exception\CacheException      as CacheException;
use Psr\Cache\CacheItemPoolInterface              as Cache;
use Psr\Http\Message\ResponseInterface            as Response;
use Psr\Http\Message\ServerRequestInterface       as Request;

class LoginController
{
    protected readonly string $baseURI;
    protected readonly LoginAction $action;
    protected readonly Cache $appCache;
    protected readonly string $statePrefix;
    protected readonly string $noncePrefix;


    /**
     * @param string $baseURI
     * @param LoginAction $action
     * @param Cache $appCache
     * @param string $statePrefix
     * @param string $noncePrefix
     */
    public function __construct(
        string $baseURI,
        LoginAction $action,
        Cache $appCache,
        string $statePrefix = 'state-',
        string $noncePrefix = 'nonce-'
    ) {
        $this->baseURI = $baseURI;
        $this->action = $action;
        $this->appCache = $appCache;
        $this->statePrefix = $statePrefix;
        $this->noncePrefix = $noncePrefix;
    }


    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function login(
        Request $request,
        Response $response
    ): Response {
        $params = $request->getParsedBody();
        if (is_object($params)) {
            $params = get_object_vars($params);
        }
        if (!is_array($params)) {
            throw new BadRequestException(message: 'Unable to read params', request: $request);
        }
        /** @var array<string,mixed> $params */

        $iss = isset($params['iss']) ? strval($params['iss']) : '';
        if ($iss == '') {
            throw new BadRequestException(message: 'Missing \'iss\' parameter', request: $request);
        }
        $clientID = isset($params['client_id']) ? strval($params['client_id']) : '';
        if ($clientID == '') {
            throw new BadRequestException(message: 'Missing \'client_id\' parameter', request: $request);
        }
        $deploymentID = isset($params['lti_deployment_id']) ? strval($params['lti_deployment_id']) : '';
        if ($deploymentID == '') {
            throw new BadRequestException(message: 'Missing \'lti_deployment_id\' parameter', request: $request);
        }
        $loginHint = isset($params['login_hint']) ? strval($params['login_hint']) : '';
        if ($loginHint == '') {
            throw new BadRequestException(message: 'Missing \'login_hint\' parameter', request: $request);
        }
        $messageHint = isset($params['lti_message_hint']) ? strval($params['lti_message_hint']) : null;

        $state = str_replace('.', '_', uniqid($this->statePrefix, true));
        $nonce = uniqid($this->noncePrefix, true);
        $cookie = $this->createStateCookie($state, $request->getUri()->getScheme());

        $redirectURL = $this->action->login(
            $state,
            $nonce,
            $iss,
            $clientID,
            $deploymentID,
            $loginHint,
            $messageHint,
        );

        $cached = $this->appCache->save(
            $this->appCache
                ->getItem("lti-1.3-{$state}")
                ->set($nonce)
                ->expiresAfter(60)
        );
        if (!$cached) {
            throw new CacheException();
        }

        $cached = $this->appCache->save(
            $this->appCache
                ->getItem("lti-1.3-{$nonce}")
                ->set(time() + 60)
                ->expiresAfter(60)
        );
        if (!$cached) {
            throw new CacheException();
        }

        return $response
            ->withStatus(302)
            ->withHeader('Set-Cookie', $cookie)
            ->withHeader('Location', $redirectURL);
    }


    /**
     * @param string $state
     * @param string $scheme
     *
     * @return string
     */
    protected function createStateCookie(
        string $state,
        string $scheme
    ): string {
        $cookie = [
            "HttpOnly",
            "Path={$this->baseURI}",
            "Max-Age=60"
        ];
        if ($scheme === 'https') {
            array_unshift($cookie, 'Secure', 'SameSite=none');
        }
        array_unshift($cookie, "lti-1.3-{$state}={$state}");

        return implode("; ", $cookie);
    }
}
