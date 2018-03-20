<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Exception;

/**
 * Class MethodNotAllowedException
 * @package ObjectivePHP\Middleware\Action\RestAction\Exception
 */
class MethodNotAllowedException extends RestActionException
{
    protected $code = 405;

    protected $message = "Method Not Allowed";
}
