<?php

namespace ObjectivePHP\RestAction\Exception;

/**
 * Class BadRequestException
 * @package ObjectivePHP\RestAction\Exception
 */
class BadRequestException extends RestActionException
{
    protected $code = 400;

    protected $message = "Bad Request";
}
