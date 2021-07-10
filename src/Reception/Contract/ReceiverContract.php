<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Reception\Contract;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\TypedDatasetEntityCollection;
use Heptacom\HeptaConnect\Portal\Base\Portal\Exception\UnsupportedDatasetEntityException;

abstract class ReceiverContract
{
    /**
     * @return iterable<array-key, \Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>
     */
    public function receive(
        TypedDatasetEntityCollection $entities,
        ReceiveContextInterface $context,
        ReceiverStackInterface $stack
    ): iterable {
        yield from $this->receiveCurrent($entities, $context);
        yield from $this->receiveNext($stack, $entities, $context);
    }

    /**
     * @return class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>
     */
    abstract public function supports(): string;

    protected function run(
        DatasetEntityContract $entity,
        ReceiveContextInterface $context
    ): void {
    }

    final protected function isSupported(DatasetEntityContract $entity): bool
    {
        return \is_a($entity, $this->supports(), false);
    }

    /**
     * @return iterable<array-key, \Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>
     */
    final protected function receiveNext(
        ReceiverStackInterface $stack,
        TypedDatasetEntityCollection $entities,
        ReceiveContextInterface $context
    ): iterable {
        return $stack->next($entities, $context);
    }

    /**
     * @return iterable<array-key, \Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>
     */
    final protected function receiveCurrent(
        TypedDatasetEntityCollection $entities,
        ReceiveContextInterface $context
    ): iterable {
        /** @var DatasetEntityContract $entity */
        foreach ($entities as $entity) {
            if (!$this->isSupported($entity)) {
                $context->markAsFailed($entity, new UnsupportedDatasetEntityException());

                continue;
            }

            try {
                $this->run($entity, $context);
            } catch (\Throwable $throwable) {
                $context->markAsFailed($entity, $throwable);

                continue;
            }

            yield $entity;
        }
    }

    /**
     * @return iterable<array-key, \Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>
     */
    final protected function receiveNextForExtends(
        ReceiverStackInterface $stack,
        TypedDatasetEntityCollection $entities,
        ReceiveContextInterface $context
    ): iterable {
        foreach ($this->receiveNext($stack, $entities, $context) as $key => $entity) {
            if (!$this->isSupported($entity)) {
                break;
            }

            try {
                $this->run($entity, $context);
            } catch (\Throwable $throwable) {
                $context->markAsFailed($entity, $throwable);

                break;
            }

            yield $key => $entity;
        }
    }
}
