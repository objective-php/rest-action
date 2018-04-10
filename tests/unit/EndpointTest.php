<?php

namespace Test\ObjectivePHP\Middleware\Action\RestAction;

use Codeception\Test\Unit;
use ObjectivePHP\Middleware\Action\RestAction\AbstractEndpoint;
use ObjectivePHP\Middleware\Action\RestAction\Exception\NotImplementedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class EndpointTest
 * @package Test\ObjectivePHP\Middleware\Action\RestAction
 */
class EndpointTest extends Unit
{
    /**
     * @test
     * @dataProvider allowedMethods
     *
     * Given an HTTP request is handled by an action
     * And the Request verb is OPTIONS
     * Then the Response MUST contain the Allow header line
     * And this header line MUST contain all supported verbs
     */
    public function optionsMethodMustModifyResponseHeadersWithAllowLine($methods, $verbs)
    {
        $endpoint = $this
            ->getMockBuilder(AbstractEndpoint::class)
            ->setMethods($methods)
            ->getMockForAbstractClass();

        $response = $endpoint->options(
            $this->getMockForAbstractClass(ServerRequestInterface::class),
            $this->getMockForAbstractClass(RequestHandlerInterface::class)
        );

        $verbs = explode(',', $verbs);
        sort($verbs);

        $output = explode(',', $response->getHeaderLine('Allow'));
        sort($output);

        $this->assertEquals($verbs, $output);
    }

    /**
     * @test
     * @dataProvider dataForNotImplementedMethodsMustThrowException
     */
    public function notImplementedMethodsMustThrowException($method)
    {
        $this->expectException(NotImplementedException::class);

        $endpoint = $this->getMockForAbstractClass(AbstractEndpoint::class);

        $endpoint->$method(
            $this->getMockForAbstractClass(ServerRequestInterface::class),
            $this->getMockForAbstractClass(RequestHandlerInterface::class)
        );
    }

    public function allowedMethods()
    {
        return [
            'action provide no method' => [[], 'OPTIONS'],
            'action provide all methods' => [
                ['put', 'get', 'head', 'post', 'delete','connect','trace'],
                'GET,HEAD,POST,PUT,DELETE,CONNECT,OPTIONS,TRACE'
            ],
            'action provide only get' => [['get'], 'GET,OPTIONS'],
            'action provide some methods' => [
                ['get', 'delete'],
                'GET,DELETE,OPTIONS'
            ],
            'action provide some other methods' => [
                ['trace', 'put', 'get'],
                'GET,PUT,OPTIONS,TRACE'
            ],
        ];
    }

    public function dataForNotImplementedMethodsMustThrowException()
    {
        return [
            "GET is not implemented" => ["get"],
            "HEAD is not implemented" => ["head"],
            "POST is not implemented" => ["post"],
            "PUT is not implemented" => ["put"],
            "DELETE is not implemented" => ["delete"],
            "CONNECT is not implemented" => ["connect"],
            "TRACE is not implemented" => ["trace"],
        ];
    }
}
