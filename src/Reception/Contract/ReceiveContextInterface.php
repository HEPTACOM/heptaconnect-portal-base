<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Reception\Contract;

use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalAwareInterface;

interface ReceiveContextInterface extends PortalAwareInterface
{
    /**
     * @psalm-return \ArrayAccess<array-key, mixed>|null
     */
    public function getConfig(MappingInterface $mapping): ?\ArrayAccess;

    public function markAsFailed(MappingInterface $mapping, \Throwable $throwable): void;
}
