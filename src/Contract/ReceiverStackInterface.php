<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Contract;

use Heptacom\HeptaConnect\Portal\Base\MappedDatasetEntityCollection;

interface ReceiverStackInterface
{
    /**
     * @return iterable<array-key, MappingInterface>
     */
    public function next(
        MappedDatasetEntityCollection $mappedDatasetEntities,
        ReceiveContextInterface $context
    ): iterable;
}
