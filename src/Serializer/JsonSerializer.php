<?php

namespace ObjectivePHP\Middleware\Action\RestAction\Serializer;

/**
 * Class JsonSerializer
 * @package ObjectivePHP\Middleware\Action\RestAction\Serializer
 */
class JsonSerializer implements SerializerInterface
{
    public function serialize($resource): string
    {
        return json_encode($resource);
    }
}
