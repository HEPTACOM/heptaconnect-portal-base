<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Reception;

use Heptacom\HeptaConnect\Dataset\Base\TypedDatasetEntityCollection;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\Receiver as ShorthandReceiver;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverStackInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ReceiverStack implements ReceiverStackInterface, LoggerAwareInterface
{
    /**
     * @var array<array-key, ReceiverContract>
     */
    private array $receivers;

    private LoggerInterface $logger;

    /**
     * @param iterable<array-key, ReceiverContract> $receivers
     */
    public function __construct(iterable $receivers)
    {
        $this->receivers = \iterable_to_array($receivers);
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function next(TypedDatasetEntityCollection $entities, ReceiveContextInterface $context): iterable
    {
        $receiver = \array_shift($this->receivers);

        if (!$receiver instanceof ReceiverContract) {
            return [];
        }

        $this->logger->debug(\sprintf('Execute FlowComponent receiver: %s', \get_class($receiver)));

        return $receiver->receive($entities, $context, $this);
    }

    public function listOrigins(): array
    {
        $origins = [];
        foreach ($this->receivers as $receiver) {
            $origins[] = $this->getOrigin($receiver);
        }

        return $origins;
    }

    protected function getOrigin(ReceiverContract $receiver): string
    {
        if ($receiver instanceof ShorthandReceiver) {
            $runMethod = $receiver->getRunMethod();

            if ($runMethod instanceof \Closure) {
                $reflection = new \ReflectionFunction($runMethod);

                return $reflection->getFileName() . '::run:' . $reflection->getStartLine();
            }

            $batchMethod = $receiver->getBatchMethod();

            if ($batchMethod instanceof \Closure) {
                $reflection = new \ReflectionFunction($batchMethod);

                return $reflection->getFileName() . '::batch:' . $reflection->getStartLine();
            }

            $this->logger->warning('ReceiverStack contains unconfigured short-notation explorer', [
                'code' => 1637607487,
            ]);
        }

        $reflection = new \ReflectionClass($receiver);

        return $reflection->getFileName() . ':' . $reflection->getStartLine();
    }
}
