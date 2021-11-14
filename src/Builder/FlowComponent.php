<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Builder;

use Closure;
use Heptacom\HeptaConnect\Portal\Base\Builder\Builder\EmitterBuilder;
use Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ExplorerBuilder;
use Heptacom\HeptaConnect\Portal\Base\Builder\Builder\HttpHandlerBuilder;
use Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ReceiverBuilder;
use Heptacom\HeptaConnect\Portal\Base\Builder\Builder\StatusReporterBuilder;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\Emitter;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\Explorer;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\HttpHandler;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\Receiver;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\StatusReporter;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\HttpHandlerToken;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\StatusReporterToken;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class FlowComponent implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var ExplorerToken[] */
    private static array $explorerTokens = [];

    /** @var EmitterToken[] */
    private static array $emitterTokens = [];

    /** @var ReceiverToken[] */
    private static array $receiverTokens = [];

    /** @var StatusReporterToken[] */
    private static array $statusReporterTokens = [];

    /** @var HttpHandlerToken[] */
    private static array $httpHandlerTokens = [];

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract> $type
     */
    public static function explorer(string $type, ?Closure $run = null, ?Closure $isAllowed = null): ExplorerBuilder
    {
        self::$explorerTokens[] = $token = new ExplorerToken($type);
        $builder = new ExplorerBuilder($token);

        if ($run instanceof Closure) {
            $builder->run($run);
        }

        if ($isAllowed instanceof Closure) {
            $builder->isAllowed($isAllowed);
        }

        return $builder;
    }

    /**
     * @param class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract> $type
     */
    public static function emitter(
        string $type,
        ?Closure $run = null,
        ?Closure $extend = null,
        ?Closure $batch = null
    ): EmitterBuilder {
        self::$emitterTokens[] = $token = new EmitterToken($type);
        $builder = new EmitterBuilder($token);

        if ($run instanceof Closure) {
            $builder->run($run);
        }

        if ($extend instanceof Closure) {
            $builder->extend($extend);
        }

        if ($batch instanceof Closure) {
            $builder->batch($batch);
        }

        return $builder;
    }

    /**
     * @param class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract> $type
     */
    public static function receiver(string $type, ?Closure $run = null, ?Closure $batch = null): ReceiverBuilder
    {
        self::$receiverTokens[] = $token = new ReceiverToken($type);
        $builder = new ReceiverBuilder($token);

        if ($run instanceof Closure) {
            $builder->run($run);
        }

        if ($batch instanceof Closure) {
            $builder->batch($batch);
        }

        return $builder;
    }

    public static function statusReporter(string $topic, ?Closure $run = null): StatusReporterBuilder
    {
        self::$statusReporterTokens[] = $token = new StatusReporterToken($topic);
        $builder = new StatusReporterBuilder($token);

        if ($run instanceof Closure) {
            $builder->run($run);
        }

        return $builder;
    }

    public static function httpHandler(string $path, ?Closure $run = null): HttpHandlerBuilder
    {
        self::$httpHandlerTokens[] = $token = new HttpHandlerToken($path);
        $builder = new HttpHandlerBuilder($token);

        if ($run instanceof Closure) {
            $builder->run($run);
        }

        return $builder;
    }

    /**
     * @return iterable<\Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract>
     */
    public function buildExplorers(): iterable
    {
        foreach (self::$explorerTokens as $key => $explorerToken) {
            yield new Explorer($explorerToken);
            unset(self::$explorerTokens[$key]);
        }
    }

    /**
     * @return iterable<\Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract>
     */
    public function buildEmitters(): iterable
    {
        foreach (self::$emitterTokens as $key => $emitterToken) {
            if ($emitterToken->getRun() instanceof Closure && $emitterToken->getBatch() instanceof Closure) {
                $this->logImplementationConflict('Emitter', 'run', 'batch');
            }

            yield new Emitter($emitterToken);
            unset(self::$emitterTokens[$key]);
        }
    }

    /**
     * @return iterable<\Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract>
     */
    public function buildReceivers(): iterable
    {
        foreach (self::$receiverTokens as $key => $receiverToken) {
            if ($receiverToken->getRun() instanceof Closure && $receiverToken->getBatch() instanceof Closure) {
                $this->logImplementationConflict('Receiver', 'run', 'batch');
            }

            yield new Receiver($receiverToken);
            unset(self::$receiverTokens[$key]);
        }
    }

    /**
     * @return iterable<\Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract>
     */
    public function buildStatusReporters(): iterable
    {
        foreach (self::$statusReporterTokens as $key => $statusReporterToken) {
            yield new StatusReporter($statusReporterToken);
            unset(self::$statusReporterTokens[$key]);
        }
    }

    /**
     * @return iterable<\Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract>
     */
    public function buildHttpHandlers(): iterable
    {
        foreach (self::$httpHandlerTokens as $key => $httpHandlerToken) {
            if (\is_callable($httpHandlerToken->getRun())) {
                if (\is_callable($httpHandlerToken->getOptions())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'options');
                }

                if (\is_callable($httpHandlerToken->getGet())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'get');
                }

                if (\is_callable($httpHandlerToken->getPost())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'post');
                }

                if (\is_callable($httpHandlerToken->getPatch())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'patch');
                }

                if (\is_callable($httpHandlerToken->getPut())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'put');
                }

                if (\is_callable($httpHandlerToken->getDelete())) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'delete');
                }
            }

            yield new HttpHandler($httpHandlerToken);
            unset(self::$httpHandlerTokens[$key]);
        }
    }

    public function reset(): void
    {
        self::$explorerTokens = [];
        self::$emitterTokens = [];
        self::$receiverTokens = [];
        self::$statusReporterTokens = [];
        self::$httpHandlerTokens = [];
    }

    private function logImplementationConflict(string $builder, string $method, string $dropped): void
    {
        $format = '%sBuilder: You implement both "%s" and "%s". The "%s" method will not be executed.';
        $message = \sprintf($format, $builder, $method, $dropped, $dropped);

        $this->logger->warning($message, [
            'code' => 1636791700,
            'builder' => $builder,
            'method' => $method,
            'dropped' => $dropped,
        ]);
    }
}
