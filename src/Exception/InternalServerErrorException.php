<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Exception;

/**
 * Class InternalServerErrorException
 * @package ObjectivePHP\Middleware\Action\RestAction\Exception
 */
class InternalServerErrorException extends RestActionException
{
    protected $code = 500;

    protected $message = "Internal Server Error";
}
