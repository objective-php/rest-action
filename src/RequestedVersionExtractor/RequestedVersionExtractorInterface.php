<?php

namespace ObjectivePHP\RestAction\RequestedVersionExtractor;

use ObjectivePHP\RestAction\Exception\VersionNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestedVersionProvider
 * @package ObjectivePHP\RestAction
 */
interface RequestedVersionExtractorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws VersionNotFoundException
     */
    public function extractFrom(ServerRequestInterface $request): string;
}
