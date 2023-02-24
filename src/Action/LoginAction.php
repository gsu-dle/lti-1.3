<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Action;

use GAState\Web\LTI\Exception\BadRequestException as BadRequest;

class LoginAction
{
    protected readonly string $lmsLoginURL;
    protected readonly string $issuer;
    protected readonly string $clientID;
    protected readonly string $deploymentID;
    protected readonly string $toolLaunchURL;


    /**
     * @param string $lmsLoginURL
     * @param string $issuer
     * @param string $clientID
     * @param string $deploymentID
     * @param string $toolLaunchURL
     */
    public function __construct(
        string $lmsLoginURL,
        string $issuer,
        string $clientID,
        string $deploymentID,
        string $toolLaunchURL
    ) {
        $this->lmsLoginURL = $lmsLoginURL;
        $this->issuer = $issuer;
        $this->clientID = $clientID;
        $this->deploymentID = $deploymentID;
        $this->toolLaunchURL = $toolLaunchURL;
    }


    /**
     * @param string $state
     * @param string $nonce
     * @param string $issuer
     * @param string $clientID
     * @param string $deploymentID
     * @param string $loginHint
     * @param ?string $messageHint
     *
     * @return string
     *
     * @throws BadRequest
     */
    public function login(
        string $state,
        string $nonce,
        string $issuer,
        string $clientID,
        string $deploymentID,
        string $loginHint,
        ?string $messageHint = null
    ): string {
        if ($state === '') {
            throw new BadRequest("Missing required parameter: 'state'");
        }
        if ($nonce === '') {
            throw new BadRequest("Missing required parameter: 'nonce'");
        }
        if ($loginHint === '') {
            throw new BadRequest("Missing required parameter: 'loginHint'");
        }
        if ($messageHint === '') {
            throw new BadRequest("'messageHint' must be a non-empty string or null");
        }
        if ($issuer !== $this->issuer) {
            throw new BadRequest("Issuer does not match configured value: '{$issuer}'");
        }
        if ($clientID !== $this->clientID) {
            throw new BadRequest("Client ID does not match configured value: '{$clientID}'");
        }
        if ($deploymentID !== $this->deploymentID) {
            throw new BadRequest("Deployment ID does not match configured value: '{$deploymentID}'");
        }

        $params = [
            'scope'            => 'openid',             // OIDC Scope.
            'response_type'    => 'id_token',           // OIDC response is always an id token.
            'response_mode'    => 'form_post',          // OIDC response is always a form post.
            'prompt'           => 'none',               // Don't prompt user on redirect.
            'client_id'        => $clientID,            // Registered client id.
            'redirect_uri'     => $this->toolLaunchURL, // URL to return to after login.
            'login_hint'       => $loginHint,           // Login hint to identify platform session.
            'state'            => $state,               // State to identify browser session.
            'nonce'            => $nonce,               // Prevent replay attacks.
        ];
        if ($messageHint !== null) {
            $params['lti_message_hint'] = $messageHint;
        }

        return "{$this->lmsLoginURL}?" . http_build_query($params);
    }
}
