<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Exception;

/**
 * Class VersionNotFoundException
 * @package ObjectivePHP\Middleware\Action\RestAction\Exception
 */
class VersionNotFoundException extends BadRequestException
{
    protected $message = "Endpoint Version Not Found In Request";
}
