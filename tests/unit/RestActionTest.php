<?php

namespace Test\ObjectivePHP\Middleware\Action\RestAction;

use Codeception\Test\Unit;
use ObjectivePHP\Middleware\Action\RestAction\Exception\MethodNotAllowedException;
use ObjectivePHP\Middleware\Action\RestAction\RestAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RestActionTest
 * @package Test\ObjectivePHP\Middleware\Action\RestAction
 */
class RestActionTest extends Unit
{
    /**
     * @test
     * @dataProvider verbsRouting
     *
     * Given an HTTP request is handled by an action
     * Then the process must call the matching action's method
     */
    public function processMustRouteMethodAccordingToRequestVerb($verb, $method)
    {
        $spyAction = $this
            ->getMockBuilder(RestAction::class)
            ->setMethods([$method])
            ->getMockForAbstractClass()
        ;
        $spyAction->expects($this->once())->method($method);

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn($verb);

        $spyAction->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class));
    }

    /**
     * @test
     * @dataProvider verbsRouting
     *
     * Given an HTTP request is handled by an action
     * And that action don't implement the method provided in the request
     * Then it must throw a MethodNotAllowedException
     */
    public function processMustThrowMethodNotAllowedWhenMethodIsMissing($verb, $method)
    {
        if ($verb == 'OPTIONS') {
            $this->markTestSkipped("The options() method is provided in RestAction, it will always be ALLOWED.");
        }

        $this->expectException(MethodNotAllowedException::class);

        $action = $this->getMockForAbstractClass(RestAction::class);

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn($verb);

        $action->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class));
    }

    /**
     * @test
     *
     * Given the action's method return a Response
     * Then the process must return this response without applying any transformation
     */
    public function processMustNotTransformAResponse()
    {
        $action = $this
            ->getMockBuilder(RestAction::class)
            ->setMethods(['get', 'serialize'])
            ->getMockForAbstractClass();
        $action->method('get')->willReturn($this->getMockForAbstractClass(ResponseInterface::class));
        $action->expects($this->never())->method('serialize');

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        $action->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class));
    }

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
        $action = $this
            ->getMockBuilder(RestAction::class)
            ->setMethods($methods)
            ->getMockForAbstractClass();

        $response = $action->options(
            $this->getMockForAbstractClass(ServerRequestInterface::class),
            $this->getMockForAbstractClass(RequestHandlerInterface::class)
        );

        $this->assertEquals($verbs, $response->getHeaderLine('Allow'));
    }

    public function verbsRouting()
    {
        return [
            'GET' => ['GET', 'get'],
            'HEAD' => ['HEAD', 'head'],
            'POST' => ['POST', 'post'],
            'PUT' => ['PUT', 'put'],
            'DELETE' => ['DELETE', 'delete'],
            'CONNECT' => ['CONNECT', 'connect'],
            'OPTIONS' => ['OPTIONS', 'options'],
            'TRACE' => ['TRACE', 'trace'],
        ];
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
}
