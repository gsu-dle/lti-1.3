<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Controller;

use GAState\Web\LTI\Util\JWKS               as JWKS;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface      as Response;

class JWKSController
{
    protected readonly JWKS $jwks;


    /**
     * @param JWKS $jwks
     */
    public function __construct(JWKS $jwks)
    {
        $this->jwks = $jwks;
    }


    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function jwks(
        Request $request,
        Response $response
    ): Response {
        $response->getBody()->write($this->jwks->__toString());
        return $response->withHeader('Content-Type', 'application/json');
    }
}
