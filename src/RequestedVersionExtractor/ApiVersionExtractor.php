<?php

namespace ObjectivePHP\RestAction\RequestedVersionExtractor;

use Composer\Semver\VersionParser;
use ObjectivePHP\RestAction\Exception\VersionNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ApiVersionExtractor
 * @package ObjectivePHP\RestAction\RequestedVersionProvider
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

        // search in headers first
        if ($request->hasHeader($this->versionHeader)) {
            $version = $request->getHeader($this->versionHeader)[0];
        }

        $version = $request->getQueryParams()['version'] ?? $version;

        // search for version in Request attributes
        if ($request->getAttribute($this->versionAttribute)) {
            $version = $request->getAttribute($this->versionAttribute);
        }

        if (empty($version)) {
            throw new VersionNotFoundException(<<<MESSAGE
Unable to find the version. The version is read by order of priority in the request's attribute "API-VERSION", then in
the request's query string "version" and at last in the request's header "API-VERSION".
MESSAGE
                , 500);
        }

        try {
            (new VersionParser())->parseConstraints($version);
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
