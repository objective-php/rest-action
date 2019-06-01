<?php

namespace ObjectivePHP\RestAction\Exception;

/**
 * Class InternalServerErrorException
 * @package ObjectivePHP\RestAction\Exception
 */
class InternalServerErrorException extends RestActionException
{
    protected $code = 500;

    protected $message = "Internal Server Error";
}
