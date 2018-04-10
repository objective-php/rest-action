<?php

namespace ObjectivePHP\Middleware\Action\RestAction;

use ObjectivePHP\Middleware\Action\RestAction\Exception\NotImplementedException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Zend\Diactoros\Response;

/**
 * Class AbstractEndpoint
 * @package ObjectivePHP\Middleware\Action\RestAction
 */
abstract class AbstractEndpoint implements EndpointInterface
{
    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function get(ServerRequestInterface $request)
    {
        throw new NotImplementedException("GET method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function head(ServerRequestInterface $request)
    {
        throw new NotImplementedException("HEAD method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function post(ServerRequestInterface $request)
    {
        throw new NotImplementedException("POST method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function put(ServerRequestInterface $request)
    {
        throw new NotImplementedException("PUT method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function delete(ServerRequestInterface $request)
    {
        throw new NotImplementedException("DELETE method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function connect(ServerRequestInterface $request)
    {
        throw new NotImplementedException("CONNECT method not available for this resource.");
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function options(ServerRequestInterface $request)
    {
        $verbs = ['get', 'head', 'post', 'put', 'delete', 'connect', 'options', 'trace'];
        $allowed = ['OPTIONS'];

        $reflexion = new \ReflectionObject($this);
        $methods = $reflexion->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (in_array($method->getName(), $verbs) && $method->getDeclaringClass()->getName() !== self::class) {
                $allowed[] = strtoupper($method->getName());
            }
        }

        $response = new Response();

        return $response->withAddedHeader('Allow', implode(",", $allowed));
    }

    /**
     * @param ServerRequestInterface $request
     * @throws NotImplementedException
     */
    public function trace(ServerRequestInterface $request)
    {
        throw new NotImplementedException("TRACE method not available for this resource.");
    }
}
