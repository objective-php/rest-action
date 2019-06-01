<?php

namespace ObjectivePHP\RestAction;

use ObjectivePHP\RestAction\Serializer\SerializerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Interface RestActionInterface
 * @package ObjectivePHP\RestAction
 */
interface RestActionInterface extends MiddlewareInterface
{
    /**
     * A RestAction is not an Endpoint as it support versioned API.
     * For a given resource the actual Endpoint is the Action in the proper Version.
     *
     * @param string $version
     * @param string $endpoint
     * @return AbstractRestAction
     */
    public function registerEndpoint(string $version, string $endpoint): RestActionInterface;

    /**
     * A RestAction supports proactive content negotiation.
     *
     * @param string $mediaType
     * @param SerializerInterface $serializer
     * @return AbstractRestAction
     */
    public function registerSerializer(string $mediaType, SerializerInterface $serializer): RestActionInterface;
}
