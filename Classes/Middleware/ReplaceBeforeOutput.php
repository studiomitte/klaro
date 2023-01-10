<?php

declare(strict_types=1);

namespace StudioMitte\Klaro\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Stream;

class ReplaceBeforeOutput implements MiddlewareInterface
{

    /**
     * @var string[]
     */
    protected array $searchAndReplacements = [
        'href="https://KLARO_CONSENT.com"' => 'href="#" onClick="return klaro.show(klaroConfig)"',
        'href="https://KLARO_RESET.com"' => 'href="#" onClick="klaro.getManager().resetConsent();location.reload()"',
    ];

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!($GLOBALS['TSFE'] ?? false)) {
            return $handler->handle($request);
        }

        // let it generate a response
        $response = $handler->handle($request);
        if ($response instanceof NullResponse) {
            return $response;
        }

        // extract the content
        $body = $response->getBody();
        $body->rewind();
        $content = $response->getBody()->getContents();

        $content = str_replace(array_keys($this->searchAndReplacements), array_values($this->searchAndReplacements), $content);

        // push new content back into the response
        $body = new Stream('php://temp', 'rw');
        $body->write($content);
        return $response->withBody($body);
    }

}
