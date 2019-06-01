<?php

namespace ObjectivePHP\RestAction\Exception;

/**
 * Class NotImplementedException
 * @package ObjectivePHP\RestAction\Exception
 */
class NotImplementedException extends RestActionException
{
    protected $code = 501;

    protected $message = "Not Implemented";
}
