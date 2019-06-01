<?php

namespace ObjectivePHP\RestAction\Serializer;

/**
 * Class JsonSerializer
 * @package ObjectivePHP\RestAction\Serializer
 */
class JsonSerializer implements SerializerInterface
{
    public function serialize($resource): string
    {
        return json_encode($resource);
    }
}
