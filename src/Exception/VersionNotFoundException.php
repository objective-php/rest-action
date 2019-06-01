<?php

namespace ObjectivePHP\RestAction\Exception;

/**
 * Class VersionNotFoundException
 * @package ObjectivePHP\RestAction\Exception
 */
class VersionNotFoundException extends BadRequestException
{
    protected $message = "Endpoint Version Not Found In Request";
}
