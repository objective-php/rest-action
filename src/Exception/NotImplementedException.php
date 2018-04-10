<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Exception;

/**
 * Class NotImplementedException
 * @package ObjectivePHP\Middleware\Action\RestAction\Exception
 */
class NotImplementedException extends RestActionException
{
    protected $code = 501;

    protected $message = "Not Implemented";
}
