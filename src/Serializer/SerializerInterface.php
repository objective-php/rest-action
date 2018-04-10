<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Serializer;

/**
 * Class SerializerInterface
 * @package ObjectivePHP\Middleware\Action\RestAction
 */
interface SerializerInterface
{
    public function serialize($resource): string;
}
