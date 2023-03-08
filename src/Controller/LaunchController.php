<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Controller;

use GAState\Web\LTI\Action\LaunchAction           as LaunchAction;
use GAState\Web\LTI\Exception\BadRequestException as BadRequestException;
use GAState\Web\LTI\Exception\CacheException      as CacheException;
use GAState\Web\LTI\Model\Message;
use Psr\Cache\CacheItemPoolInterface              as Cache;
use Psr\Http\Message\ServerRequestInterface       as Request;
use Psr\Http\Message\ResponseInterface            as Response;
use Throwable;

class LaunchController
{
    protected readonly string $baseURI;
    protected readonly LaunchAction $action;
    protected readonly Cache $appCache;
    protected readonly string $launchPrefix;
    protected readonly string $statePrefix;
    protected readonly string $noncePrefix;


    /**
     * @param string $baseURI
     * @param LaunchAction $action
     * @param Cache $appCache
     * @param string $launchPrefix
     * @param string $statePrefix
     * @param string $noncePrefix
     */
    public function __construct(
        string $baseURI,
        LaunchAction $action,
        Cache $appCache,
        string $launchPrefix = 'launch-',
        string $statePrefix = 'state-',
        string $noncePrefix = 'nonce-'
    ) {
        $this->baseURI = $baseURI;
        $this->action = $action;
        $this->appCache = $appCache;
        $this->launchPrefix = $launchPrefix;
        $this->statePrefix = $statePrefix;
        $this->noncePrefix = $noncePrefix;
    }


    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getMessage(
        Request $request,
        Response $response
    ): Response {
        try {
            $response = $response->withHeader('Content-Type', 'application/json');

            $cookies = $request->getCookieParams();
            $launchID = strval($cookies["lti-1_3-launch"] ?? '');
            $message = $this->appCache->getItem("lti-1_3-{$launchID}")->get();
            if ($message instanceof Message) {
                $response = $response->withStatus(200);
                $response->getBody()->write(
                    strval(json_encode($message, JSON_PRETTY_PRINT))
                );
            } else {
                //$response = $response->withStatus(404);
                $response->getBody()->write(
                    strval(json_encode(["error" => "Message not found"]))
                );
            }
        } catch (Throwable $t) {
            $response->getBody()->write(
                strval(json_encode(["error" => "Message not found"]))
            );
        }

        return $response;
    }


    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function launch(
        Request $request,
        Response $response
    ): Response {
        $cookies = $request->getCookieParams();

        $params = $request->getParsedBody();
        if (!is_array($params)) {
            throw new BadRequestException(message: 'Unable to read params', request: $request);
        }

        $idToken = strval($params['id_token'] ?? '');
        if ($idToken == '') {
            throw new BadRequestException(message: 'Missing \'id_token\' parameter', request: $request);
        }
        $idTokenState = strval($params['state'] ?? '');
        if ($idTokenState == '') {
            throw new BadRequestException(message: 'Missing \'state\' parameter', request: $request);
        }
        $state = strval($cookies["lti-1_3-{$idTokenState}"] ?? '');
        if ($state == '') {
            throw new BadRequestException(message: 'Missing \'state\' cookie', request: $request);
        }
        $nonce = strval($this->appCache->getItem("lti-1_3-{$idTokenState}")->get());
        if ($nonce == '') {
            throw new BadRequestException(message: 'Missing \'nonce\' value', request: $request);
        }

        $launchID = str_replace('.', '_', uniqid($this->launchPrefix, true));

        // Generate launch message
        $message = $this->action->launch(
            $launchID,
            $state,
            $nonce,
            $idToken,
            $idTokenState
        );

        // Cache message
        $cached = $this->appCache->save(
            $this->appCache
                ->getItem("lti-1_3-{$message->launchID}")
                ->set($message)
                ->expiresAfter(2 * 60 * 60)
        );
        if (!$cached) {
            throw new CacheException();
        }

        // Set `launchID` in a cookie and redirect
        $cookie = $this->createLaunchCookie($message->launchID, $request->getUri()->getScheme());
        return $response
            ->withStatus(302)
            ->withHeader('Set-Cookie', $cookie)
            ->withHeader('Location', $message->targetLinkURI);
    }


    /**
     * @param string $launchID
     * @param string $scheme
     *
     * @return string
     */
    protected function createLaunchCookie(
        string $launchID,
        string $scheme
    ): string {
        $cookie = [
            "HttpOnly",
            "Path={$this->baseURI}",
            "Max-Age=" . (2 * 60 * 60)
        ];
        if ($scheme === 'https') {
            array_unshift($cookie, 'Secure', 'SameSite=none');
        }
        array_unshift($cookie, "lti-1_3-launch={$launchID}");

        return implode("; ", $cookie);
    }
}
