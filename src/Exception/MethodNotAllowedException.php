<?php

namespace ObjectivePHP\RestAction\Exception;

/**
 * Class MethodNotAllowedException
 * @package ObjectivePHP\RestAction\Exception
 */
class MethodNotAllowedException extends RestActionException
{
    protected $code = 405;

    protected $message = "Method Not Allowed";
}
