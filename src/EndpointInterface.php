<?php

namespace ObjectivePHP\RestAction;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface EndpointInterface
 * @package ObjectivePHP\RestAction
 */
interface EndpointInterface
{
    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.1
     */
    public function get(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.2
     */
    public function head(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.3
     */
    public function post(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.4
     */
    public function put(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.5
     */
    public function delete(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.6
     */
    public function connect(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.7
     */
    public function options(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     * @see https://tools.ietf.org/html/rfc7231#section-4.3.8
     */
    public function trace(ServerRequestInterface $request);
}
