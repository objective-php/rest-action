<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Exception;

/**
 * Class BadRequestException
 * @package ObjectivePHP\Middleware\Action\RestAction\Exception
 */
class BadRequestException extends RestActionException
{
    protected $code = 400;

    protected $message = "Bad Request";
}
