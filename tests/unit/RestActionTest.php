<?php

namespace Test\ObjectivePHP\Middleware\Action\RestAction;

use Codeception\Test\Unit;
use ObjectivePHP\Middleware\Action\RestAction\AbstractEndpoint;
use ObjectivePHP\Middleware\Action\RestAction\AbstractRestAction;
use ObjectivePHP\Middleware\Action\RestAction\Exception\NotImplementedException;
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
        $spyEndpoint = $this
            ->getMockBuilder(AbstractEndpoint::class)
            ->setMethods([$method])
            ->getMockForAbstractClass()
        ;
        $spyEndpoint->expects($this->once())->method($method);

        $action = $this
            ->getMockBuilder(AbstractRestAction::class)
            ->setMethods(['buildResponse', 'getEndpointInstance', 'getRequestedVersionExtractor'])
            ->getMockForAbstractClass()
        ;
        $action
            ->method('getEndpointInstance')
            ->willReturn($spyEndpoint);
        $action->method('buildResponse')->willReturn($this->getMockForAbstractClass(ResponseInterface::class));

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn($verb);

        $action->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class));
    }

    /**
     * @test
     * @dataProvider verbsRouting
     *
     * Given an HTTP request is handled by an action
     * And that action don't implement the method provided in the request
     * Then it must throw a MethodNotAllowedException
     */
    public function processMustThrowNotImplementedWhenMethodIsMissing($verb, $method)
    {
        if ($verb == 'OPTIONS') {
            $this->markTestSkipped("The options() method is provided in RestAction, it will always be ALLOWED.");
        }

        $this->expectException(NotImplementedException::class);

        $dummyEndpoint = $this->getMockForAbstractClass(AbstractEndpoint::class);

        $action = $this
            ->getMockBuilder(AbstractRestAction::class)
            ->setMethods(['getEndpointInstance', 'getRequestedVersionExtractor'])
            ->getMockForAbstractClass()
        ;
        $action
            ->method('getEndpointInstance')
            ->willReturn($dummyEndpoint);

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn($verb);

        $action->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class));
    }

    /**
     * @test
     */
    public function processMustNotChangeTheResponseWhenEndpointReturnResponseObject()
    {
        $response = $this->getMockForAbstractClass(ResponseInterface::class);

        $endpoint = $this
            ->getMockBuilder(AbstractEndpoint::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();
        $endpoint->method('get')->willReturn($response);


        $action = $this
            ->getMockBuilder(AbstractRestAction::class)
            ->setMethods(['getEndpointInstance', 'getRequestedVersionExtractor'])
            ->getMockForAbstractClass();
        $action->method('getEndpointInstance')->willReturn($endpoint);

        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('get');

        $this->assertSame(
            $response,
            $action->process($request, $this->getMockForAbstractClass(RequestHandlerInterface::class))
        );
    }

    /**
     * @test
     * @dataProvider dataForGetEndpointInstanceMustThrowExceptionWhenNoEndpointFound
     */
    public function getEndpointInstanceMustThrowExceptionWhenNoEndpointFound($requestedVersion)
    {
        $this->expectException(NotImplementedException::class);

        $action = $this->getMockForAbstractClass(AbstractRestAction::class);

        $action
            ->registerEndpoint("1", "Not\\Revelant")
            ->registerEndpoint("1.2.3", "Not\\Revelant")
            ->registerEndpoint("3.0.0", "Not\\Revelant")
        ;

        $action->getEndpointInstance($requestedVersion);
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

    public function dataForGetEndpointInstanceMustThrowExceptionWhenNoEndpointFound()
    {
        return [
            ["2.2.2"],
            ["^1.4"],
        ];
    }
}
