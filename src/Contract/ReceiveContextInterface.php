<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Contract;

interface ReceiveContextInterface extends PortalNodeAwareInterface
{
    /**
     * @psalm-return \ArrayAccess<array-key, mixed>|null
     */
    public function getConfig(MappingInterface $mapping): ?\ArrayAccess;

    public function markAsFailed(MappingInterface $mapping, \Throwable $throwable): void;
}
