<?php

namespace ObjectivePHP\Middleware\Action\RestAction\RequestedVersionExtractor;

use ObjectivePHP\Middleware\Action\RestAction\Exception\VersionNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestedVersionProvider
 * @package ObjectivePHP\Middleware\Action\RestAction
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
