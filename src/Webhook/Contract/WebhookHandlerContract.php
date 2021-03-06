<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Webhook\Contract;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class WebhookHandlerContract
{
    protected ResponseFactoryInterface $responseFactory;

    protected StreamFactoryInterface $streamFactory;

    public function __construct()
    {
        $this->responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    public function handle(RequestInterface $request, WebhookContextInterface $context): ResponseInterface
    {
        return $this->responseFactory->createResponse(404);
    }

    abstract protected function getDecorated(): WebhookHandlerContract;
}
