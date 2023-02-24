<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Exception;

use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class BadRequestException extends LTIException
{
    protected ?Request $request;


    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param ?Request $request
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?Request $request = null,
    ) {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }


    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }


    /**
     * @param Request|null $request
     * @return void
     */
    public function setRequest(?Request $request): void
    {
        $this->request = $request;
    }
}
