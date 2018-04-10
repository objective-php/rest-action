<?php

namespace ObjectivePHP\Middleware\Action\RestAction\RequestedVersionExtractor;

use Composer\Semver\VersionParser;
use ObjectivePHP\Middleware\Action\RestAction\Exception\VersionNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ApiVersionExtractor
 * @package ObjectivePHP\Middleware\Action\RestAction\RequestedVersionProvider
 */
class ApiVersionExtractor implements RequestedVersionExtractorInterface
{
    protected $versionAttribute = 'API-VERSION';

    protected $versionHeader = 'API-VERSION';

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws VersionNotFoundException
     */
    public function extractFrom(ServerRequestInterface $request): string
    {
        $version = null;

        // search for version in Request attributes
        if ($request->getAttribute($this->versionAttribute)) {
            $version = $request->getAttribute($this->versionAttribute);
        }

        // search in headers
        if ($request->hasHeader($this->versionHeader)) {
            $version = $request->getHeader($this->versionHeader);
        }

        $parser = new VersionParser();
        try {
            $parser->parseConstraints($version);
        } catch (\UnexpectedValueException $e) {
            throw new VersionNotFoundException(
                sprintf("Unable to understand the given version `%s`", $version),
                500,
                $e
            );
        }

        return $version;
    }
}
