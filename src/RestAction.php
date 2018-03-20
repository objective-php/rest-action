<?php

namespace ObjectivePHP\Middleware\Action\RestAction;

use ObjectivePHP\Middleware\Action\RestAction\Exception\MethodNotAllowedException;
use ObjectivePHP\Middleware\HttpAction\HttpAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

/**
 * Class RestAction
 * @package ObjectivePHP\Middleware\Action\RestAction
 */
abstract class RestAction extends HttpAction
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        if (!method_exists($this, $method)) {
            throw new MethodNotAllowedException();
        }
        $response = $this->$method($request, $handler);

        if (!$response instanceof ResponseInterface) {
            // @TODO handle Response build
            $response = new Response();
        }

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function options(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $options = [
            'get',
            'head',
            'post',
            'put',
            'delete',
            'connect',
            'options',
            'trace',
        ];

        $allowed = [];

        foreach ($options as $option) {
            if (method_exists($this, $option)) {
                $allowed[] = strtoupper($option);
            }
        }

        return (new Response())->withAddedHeader('Allow', implode(",", $allowed));
    }
}
