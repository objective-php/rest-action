<?php
namespace Test\ObjectivePHP\Middleware\Action\RestAction\RequestedVersionProvider;

use Codeception\Test\Unit;
use ObjectivePHP\Middleware\Action\RestAction\Exception\VersionNotFoundException;
use ObjectivePHP\Middleware\Action\RestAction\RequestedVersionExtractor\ApiVersionExtractor;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ApiVersionExtractorTest
 */
class ApiVersionExtractorTest extends Unit
{
    /**
     * @test
     */
    public function extractorMustLookInRequestAttributes()
    {
        $extractor = new ApiVersionExtractor();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['getAttribute'])
            ->getMockForAbstractClass();

        $request->method('getAttribute')->willReturn('12.3.56');

        $this->assertEquals('12.3.56', $extractor->extractFrom($request));
    }

    /**
     * @test
     */
    public function extractorMustLookInRequestHeaders()
    {
        $extractor = new ApiVersionExtractor();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['hasHeader', 'getHeader'])
            ->getMockForAbstractClass();

        $request->method('hasHeader')->willReturn(true);
        $request->method('getHeader')->willReturn(['12.3.56', 'dummy']);

        $this->assertEquals('12.3.56', $extractor->extractFrom($request));
    }

    /**
     * @test
     */
    public function extractorMustLookInRequestQueryString()
    {
        $extractor = new ApiVersionExtractor();

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['getQueryParams'])
            ->getMockForAbstractClass();

        $request->method('getQueryParams')->willReturn(['version' => '1.2.3']);

        $this->assertEquals('1.2.3', $extractor->extractFrom($request));
    }

    /**
     * @test
     */
    public function extractorMustThrowExceptionIfNoSemverVersionFound()
    {
        $this->expectException(VersionNotFoundException::class);
        (new ApiVersionExtractor())->extractFrom($this->getMockForAbstractClass(ServerRequestInterface::class));
    }

    /**
     * @test
     */
    public function extractorMustThrowExceptionIfNoSemverVersionWasValid()
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['getAttribute'])
            ->getMockForAbstractClass();

        $request->method('getAttribute')->willReturn('test');

        $this->expectException(VersionNotFoundException::class);
        (new ApiVersionExtractor())->extractFrom($request);
    }
}
