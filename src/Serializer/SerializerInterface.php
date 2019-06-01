<?php

namespace ObjectivePHP\RestAction\Serializer;

/**
 * Class SerializerInterface
 * @package ObjectivePHP\RestAction
 */
interface SerializerInterface
{
    public function serialize($resource): string;
}
