<?php

namespace ObjectivePHP\RestAction;

use Aura\Accept\AcceptFactory;
use Composer\Semver\Semver;
use ObjectivePHP\RestAction\Exception\InternalServerErrorException;
use ObjectivePHP\RestAction\Exception\NotImplementedException;
use ObjectivePHP\RestAction\Exception\VersionNotFoundException;
use ObjectivePHP\RestAction\RequestedVersionExtractor\ApiVersionExtractor;
use ObjectivePHP\RestAction\RequestedVersionExtractor\RequestedVersionExtractorInterface;
use ObjectivePHP\RestAction\Serializer\SerializerInterface;
use ObjectivePHP\HttpAction\HttpAction;
use ObjectivePHP\ServicesFactory\Exception\ServicesFactoryException;
use ObjectivePHP\ServicesFactory\ServicesFactoryAccessorsTrait;
use ObjectivePHP\ServicesFactory\ServicesFactoryAwareInterface;
use ObjectivePHP\ServicesFactory\ServicesFactoryProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

/**
 * Class RestAction
 * @package ObjectivePHP\RestAction
 */
abstract class AbstractRestAction extends HttpAction implements
    RestActionInterface,
    ServicesFactoryProviderInterface,
    ServicesFactoryAwareInterface
{
    use ServicesFactoryAccessorsTrait;

    /**
     * @var RequestedVersionExtractorInterface
     */
    protected $requestedVersionExtractor;

    /**
     * @var EndpointInterface[]
     */
    protected $endpoints = [];

    /**
     * @var SerializerInterface[]
     */
    protected $serializers = [];

    /**
     * @var string
     */
    protected $matchedVersion;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws InternalServerErrorException
     * @throws NotImplementedException
     * @throws VersionNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $endpoint = $this->getEndpointInstance($this->getVersionFrom($request));

        $method = $request->getMethod();
        $resource = $endpoint->$method($request, $handler);

        if (!$resource instanceof ResponseInterface) {
            return $this->buildResponse($request, $resource);
        }

        return $resource;
    }


    public function registerEndpoint(string $version, string $endpoint): RestActionInterface
    {
        $this->endpoints[$version] = $endpoint;
        return $this;
    }

    /**
     * @param string $version
     * @return EndpointInterface
     * @throws InternalServerErrorException
     * @throws NotImplementedException
     */
    public function getEndpointInstance(string $version): EndpointInterface
    {
        // Finding the best candidate Endpoint
        $endpoints = Semver::satisfiedBy(array_keys($this->endpoints), $version);
        if (empty($endpoints)) {
            throw new NotImplementedException(sprintf("No version %s for this endpoint", $version));
        }

        // Building the Endpoint
        $this->matchedVersion = array_pop($endpoints);

        $fullyQualifiedClassName = $this->endpoints[$this->matchedVersion];
        if (!class_exists($fullyQualifiedClassName)) {
            throw new InternalServerErrorException("Unable to create the endpoint");
        }
        $endpoint = new $fullyQualifiedClassName();

        try {
            $this->getServicesFactory()->injectDependencies($endpoint);
        } catch (ServicesFactoryException $e) {
            throw new InternalServerErrorException("Unable to build the endpoint", 500, $e);
        }

        return $endpoint;
    }

    /**
     * @param ServerRequestInterface $request
     * @param mixed $resource
     * @return Response
     */
    public function buildResponse(ServerRequestInterface $request, $resource)
    {
        // We use Aura.accept to handle proactive content negotiation.
        // Aura.accept use $_SERVER access headers but we may want to use the PSR request later.
        // That's why, for now, the first parameter $request is unused.
        $accept_factory = new AcceptFactory($_SERVER);
        $accept = $accept_factory->newInstance();
        $contentType = $accept->negotiateMedia($this->getAvailableMedias());

        $contentType = $contentType ? $contentType->getValue() : $this->getAvailableMedias()[0];

        $serializer = $this->getSerializer($contentType);

        $body = $serializer->serialize($resource);

        $response = new Response();
        $response = $response->withAddedHeader('ContentType', $contentType);

        $response = $response->withAddedHeader('API-MATCHED-VERSION', $this->matchedVersion);

        $response->getBody()->write($body);
        $response->getBody()->rewind();

        return $response;
    }

    public function getAvailableMedias(): array
    {
        return array_keys($this->serializers);
    }

    public function getSerializer(string $media): SerializerInterface
    {
        return $this->serializers[$media];
    }

    public function registerSerializer(string $media, SerializerInterface $serializer) : RestActionInterface
    {
        $this->serializers[$media] = $serializer;
        return $this;
    }

    /**
     * Get the RestAction's getRequestedVersionExtractor.
     *
     * @return RequestedVersionExtractorInterface
     */
    public function getRequestedVersionExtractor(): RequestedVersionExtractorInterface
    {
        if (is_null($this->requestedVersionExtractor)) {
            $this->requestedVersionExtractor = new ApiVersionExtractor();
        }

        return $this->requestedVersionExtractor;
    }

    /**
     * Set the RestAction's RequestedVersionProvider.
     *
     * @param RequestedVersionExtractorInterface $requestedVersionExtractor
     * @return $this
     */
    public function setRequestedVersionExtractor(RequestedVersionExtractorInterface $requestedVersionExtractor)
    {
        $this->requestedVersionExtractor = $requestedVersionExtractor;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws VersionNotFoundException
     */
    protected function getVersionFrom(ServerRequestInterface $request)
    {
        return $this->getRequestedVersionExtractor()->extractFrom($request);
    }

    /**
     * @return string
     */
    public function getMatchedVersion()
    {
        return $this->matchedVersion;
    }

}
