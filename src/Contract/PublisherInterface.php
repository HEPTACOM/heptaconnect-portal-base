<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Contract;

interface PublisherInterface
{
    /**
     * @param class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface> $datasetEntityClassName
     */
    public function publish(string $datasetEntityClassName, string $portalNodeId, string $externalId): MappingInterface;
}