<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Action;

use GAState\Web\LTI\Exception\BadRequestException as BadRequestException;
use PHPUnit\Framework\TestCase                    as TestCase;

final class LoginActionTest extends TestCase
{
    private string $lmsLoginURL;
    private string $issuer;
    private string $clientID;
    private string $deploymentID;
    private string $toolLaunchURL;

    /**
     * @var array<string,string|null> $params
     */
    private array $params = [];


    public function setUp(): void
    {
        $this->lmsLoginURL = 'https://lms/lti/authenticate';
        $this->issuer = '__iss__';
        $this->clientID = '__client_id__';
        $this->deploymentID = '__lti_deployment_id__';
        $this->toolLaunchURL = 'https://tool/lti/launch';

        $this->params = [
            'state' => '__state__',
            'nonce' => '__nonce__',
            'issuer' => '__iss__',
            'clientID' => '__client_id__',
            'deploymentID' => '__lti_deployment_id__',
            'loginHint' => '__login_hint__',
            'messageHint' => '__lti_message_hint__'
        ];
    }


    public function testLogin(): void
    {
        $redirectURL = $this->login();

        self::assertStringStartsWith($this->lmsLoginURL . "?", $redirectURL);
        self::assertStringContainsString('scope=openid', $redirectURL);
        self::assertStringContainsString('response_type=id_token', $redirectURL);
        self::assertStringContainsString('response_mode=form_post', $redirectURL);
        self::assertStringContainsString('prompt=none', $redirectURL);
        self::assertStringContainsString("client_id=" . urlencode($this->clientID), $redirectURL);
        self::assertStringContainsString("redirect_uri=" . urlencode($this->toolLaunchURL), $redirectURL);
        self::assertStringContainsString("state=" . urlencode($this->params['state'] ?? ''), $redirectURL);
        self::assertStringContainsString("nonce=" . urlencode($this->params['nonce'] ?? ''), $redirectURL);
        self::assertStringContainsString(
            "lti_message_hint=" . urlencode($this->params['messageHint'] ?? ''),
            $redirectURL
        );
    }


    public function testBlankState(): void
    {
        $this->params['state'] = '';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing required parameter: 'state'");

        $this->login();

        self::fail();
    }


    public function testBlankNonce(): void
    {
        $this->params['nonce'] = '';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing required parameter: 'nonce'");

        $this->login();

        self::fail();
    }


    public function testBlankLoginHint(): void
    {
        $this->params['loginHint'] = '';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Missing required parameter: 'loginHint'");

        $this->login();

        self::fail();
    }


    public function testBlankMessageHint(): void
    {
        $this->params['messageHint'] = '';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("'messageHint' must be a non-empty string or null");

        $this->login();

        self::fail();
    }


    public function testNullMessageHint(): void
    {
        $this->params['messageHint'] = null;

        $redirectURL = $this->login();

        self::assertStringStartsWith($this->lmsLoginURL . "?", $redirectURL);
        self::assertStringContainsString('scope=openid', $redirectURL);
        self::assertStringContainsString('response_type=id_token', $redirectURL);
        self::assertStringContainsString('response_mode=form_post', $redirectURL);
        self::assertStringContainsString('prompt=none', $redirectURL);
        self::assertStringContainsString("client_id=" . urlencode($this->clientID), $redirectURL);
        self::assertStringContainsString("redirect_uri=" . urlencode($this->toolLaunchURL), $redirectURL);
        self::assertStringContainsString("state=" . urlencode($this->params['state'] ?? ''), $redirectURL);
        self::assertStringContainsString("nonce=" . urlencode($this->params['nonce'] ?? ''), $redirectURL);
        self::assertStringNotContainsString("lti_message_hint=", $redirectURL);
    }


    public function testMismatchedIssuer(): void
    {
        $this->params['issuer'] .= 'diff';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Issuer does not match configured value: '{$this->params['issuer']}'");

        $this->login();

        self::fail();
    }


    public function testMismatchedClientID(): void
    {
        $this->params['clientID'] .= 'diff';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Client ID does not match configured value: '{$this->params['clientID']}'");

        $this->login();

        self::fail();
    }


    public function testMismatchedDeploymentID(): void
    {
        $this->params['deploymentID'] .= 'diff';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(
            "Deployment ID does not match configured value: '{$this->params['deploymentID']}'"
        );

        $this->login();

        self::fail();
    }


    /**
     * @return string
     */
    private function login(): string
    {
        return (new LoginAction(
            $this->lmsLoginURL,
            $this->issuer,
            $this->clientID,
            $this->deploymentID,
            $this->toolLaunchURL
        ))->login(
            state: $this->params['state'] ?? '',
            nonce: $this->params['nonce'] ?? '',
            issuer: $this->params['issuer'] ?? '',
            clientID: $this->params['clientID'] ?? '',
            deploymentID: $this->params['deploymentID'] ?? '',
            loginHint: $this->params['loginHint'] ?? '',
            messageHint: $this->params['messageHint']
        );
    }
}
