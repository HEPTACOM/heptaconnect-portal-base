<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Flow\DirectEmission;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\DatasetEntityCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;

abstract class DirectEmissionFlowContract
{
    /**
     * @psalm-param DatasetEntityCollection<DatasetEntityContract> $entities
     */
    abstract public function run(
        PortalNodeKeyInterface $portalNodeKey,
        DatasetEntityCollection $entities
    ): DirectEmissionResult;
}
