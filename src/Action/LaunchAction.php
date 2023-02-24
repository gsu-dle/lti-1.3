<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Action;

use Exception                                         as Exception;
use GAState\Web\LTI\Exception\InvalidIDTokenException as InvalidIDToken;
use GAState\Web\LTI\Model\Message                     as Message;
use GAState\Web\LTI\Model\MessageFactoryInterface     as MessageFactory;
use GAState\Web\LTI\Util\JWTDecoderInterface          as JWTDecoder;

class LaunchAction
{
    protected readonly string $issuer;
    protected readonly string $clientID;
    protected readonly string $deploymentID;
    protected readonly JWTDecoder $jwtDecoder;
    protected readonly MessageFactory $messageFactory;


    /**
     * @param string $issuer
     * @param string $clientID
     * @param string $deploymentID
     * @param JWTDecoder $jwtDecoder
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        string $issuer,
        string $clientID,
        string $deploymentID,
        JWTDecoder $jwtDecoder,
        MessageFactory $messageFactory
    ) {
        $this->issuer = $issuer;
        $this->clientID = $clientID;
        $this->deploymentID = $deploymentID;
        $this->jwtDecoder = $jwtDecoder;
        $this->messageFactory = $messageFactory;
    }


    /**
     * @param string $launchID
     * @param string $state
     * @param string $nonce
     * @param string $idToken
     * @param ?string $idTokenState
     *
     * @return Message
     */
    public function launch(
        string $launchID,
        string $state,
        string $nonce,
        string $idToken,
        ?string $idTokenState = null,
    ): Message {
        // decode idToken; throws InvalidIdTokenException
        try {
            $messagePayload = $this->jwtDecoder->decode($idToken);
            $messagePayload['launch_id'] = $launchID;
            $messagePayload['state'] = $idTokenState;
        } catch (Exception $ex) {
            throw new InvalidIDToken(previous: $ex);
        }

        // create LTI message; throws MessageException
        try {
            $message = $this->messageFactory->createMessage($messagePayload);
        } catch (Exception $ex) {
            throw new Exception(message: 'MessageException', previous: $ex); // TODO: replace
        }

        // validate LTI message; throws MessageValidationException
        if ($message->issuer !== $this->issuer) {
            throw new Exception("MessageValidationException"); // TODO: replace
        }
        if ($message->clientID !== $this->clientID) {
            throw new Exception("MessageValidationException"); // TODO: replace
        }
        if ($message->deploymentID !== $this->deploymentID) {
            throw new Exception("MessageValidationException"); // TODO: replace
        }
        if ($message->state !== $state) {
            throw new Exception('MessageValidationException'); // TODO: replace
        }
        if ($message->nonce !== $nonce) {
            throw new Exception('MessageValidationException'); // TODO: replace
        }

        return $message;
    }
}
