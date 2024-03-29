<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Builder;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\EntityType;
use Heptacom\HeptaConnect\Dataset\Base\Exception\InvalidClassNameException;
use Heptacom\HeptaConnect\Dataset\Base\Exception\InvalidSubtypeClassNameException;
use Heptacom\HeptaConnect\Dataset\Base\Exception\UnexpectedLeadingNamespaceSeparatorInClassNameException;
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
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class FlowComponent implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    private ?int $defaultPriority = null;

    /**
     * @var ExplorerToken[]
     */
    private static array $explorerTokens = [];

    /**
     * @var EmitterToken[]
     */
    private static array $emitterTokens = [];

    /**
     * @var ReceiverToken[]
     */
    private static array $receiverTokens = [];

    /**
     * @var StatusReporterToken[]
     */
    private static array $statusReporterTokens = [];

    /**
     * @var HttpHandlerToken[]
     */
    private static array $httpHandlerTokens = [];

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param class-string<DatasetEntityContract> $type
     *
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    public static function explorer(string $type, ?\Closure $run = null, ?\Closure $isAllowed = null): ExplorerBuilder
    {
        self::$explorerTokens[] = $token = new ExplorerToken(new EntityType($type));
        $builder = new ExplorerBuilder($token);

        if ($run instanceof \Closure) {
            $builder->run($run);
        }

        if ($isAllowed instanceof \Closure) {
            $builder->isAllowed($isAllowed);
        }

        return $builder;
    }

    /**
     * @param class-string<DatasetEntityContract> $type
     *
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    public static function emitter(
        string $type,
        ?\Closure $run = null,
        ?\Closure $extend = null,
        ?\Closure $batch = null
    ): EmitterBuilder {
        self::$emitterTokens[] = $token = new EmitterToken(new EntityType($type));
        $builder = new EmitterBuilder($token);

        if ($run instanceof \Closure) {
            $builder->run($run);
        }

        if ($extend instanceof \Closure) {
            $builder->extend($extend);
        }

        if ($batch instanceof \Closure) {
            $builder->batch($batch);
        }

        return $builder;
    }

    /**
     * @param class-string<DatasetEntityContract> $type
     *
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    public static function receiver(string $type, ?\Closure $run = null, ?\Closure $batch = null): ReceiverBuilder
    {
        self::$receiverTokens[] = $token = new ReceiverToken(new EntityType($type));
        $builder = new ReceiverBuilder($token);

        if ($run instanceof \Closure) {
            $builder->run($run);
        }

        if ($batch instanceof \Closure) {
            $builder->batch($batch);
        }

        return $builder;
    }

    public static function statusReporter(string $topic, ?\Closure $run = null): StatusReporterBuilder
    {
        self::$statusReporterTokens[] = $token = new StatusReporterToken($topic);
        $builder = new StatusReporterBuilder($token);

        if ($run instanceof \Closure) {
            $builder->run($run);
        }

        return $builder;
    }

    public static function httpHandler(string $path, ?\Closure $run = null): HttpHandlerBuilder
    {
        self::$httpHandlerTokens[] = $token = new HttpHandlerToken($path);
        $builder = new HttpHandlerBuilder($token);

        if ($run instanceof \Closure) {
            $builder->run($run);
        }

        return $builder;
    }

    /**
     * @return iterable<ExplorerContract>
     */
    public function buildExplorers(): iterable
    {
        foreach (self::$explorerTokens as $key => $token) {
            $priority = $token->getPriority() ?? $this->defaultPriority;

            yield $priority => new Explorer($token);
            unset(self::$explorerTokens[$key]);
        }
    }

    /**
     * @return iterable<EmitterContract>
     */
    public function buildEmitters(): iterable
    {
        foreach (self::$emitterTokens as $key => $token) {
            if ($token->getRun() instanceof \Closure && $token->getBatch() instanceof \Closure) {
                $this->logImplementationConflict('Emitter', 'run', 'batch');
            }

            $priority = $token->getPriority() ?? $this->defaultPriority;

            yield $priority => new Emitter($token);
            unset(self::$emitterTokens[$key]);
        }
    }

    /**
     * @return iterable<ReceiverContract>
     */
    public function buildReceivers(): iterable
    {
        foreach (self::$receiverTokens as $key => $token) {
            if ($token->getRun() instanceof \Closure && $token->getBatch() instanceof \Closure) {
                $this->logImplementationConflict('Receiver', 'run', 'batch');
            }

            $priority = $token->getPriority() ?? $this->defaultPriority;

            yield $priority => new Receiver($token);
            unset(self::$receiverTokens[$key]);
        }
    }

    /**
     * @return iterable<StatusReporterContract>
     */
    public function buildStatusReporters(): iterable
    {
        foreach (self::$statusReporterTokens as $key => $token) {
            $priority = $token->getPriority() ?? $this->defaultPriority;

            yield $priority => new StatusReporter($token);
            unset(self::$statusReporterTokens[$key]);
        }
    }

    /**
     * @return iterable<HttpHandlerContract>
     */
    public function buildHttpHandlers(): iterable
    {
        foreach (self::$httpHandlerTokens as $key => $httpHandlerToken) {
            if ($httpHandlerToken->getRun() instanceof \Closure) {
                if ($httpHandlerToken->getOptions() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'options');
                }

                if ($httpHandlerToken->getGet() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'get');
                }

                if ($httpHandlerToken->getPost() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'post');
                }

                if ($httpHandlerToken->getPatch() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'patch');
                }

                if ($httpHandlerToken->getPut() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'put');
                }

                if ($httpHandlerToken->getDelete() instanceof \Closure) {
                    $this->logImplementationConflict('HttpHandler', 'run', 'delete');
                }
            }

            $priority = $httpHandlerToken->getPriority() ?? $this->defaultPriority;

            yield $priority => new HttpHandler($httpHandlerToken);
            unset(self::$httpHandlerTokens[$key]);
        }
    }

    public function reset(): void
    {
        $this->defaultPriority = null;
        self::$explorerTokens = [];
        self::$emitterTokens = [];
        self::$receiverTokens = [];
        self::$statusReporterTokens = [];
        self::$httpHandlerTokens = [];
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setDefaultPriority(int $defaultPriority): void
    {
        $this->defaultPriority = $defaultPriority;
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
