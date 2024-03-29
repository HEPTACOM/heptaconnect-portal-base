<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Mapping;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\EntityType;
use Heptacom\HeptaConnect\Dataset\Base\EntityTypeCollection;
use Heptacom\HeptaConnect\Dataset\Base\ScalarCollection\StringCollection;
use Heptacom\HeptaConnect\Dataset\Base\Support\AbstractObjectCollection;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;

/**
 * @extends AbstractObjectCollection<MappingComponentStructContract>
 */
class MappingComponentCollection extends AbstractObjectCollection
{
    public function contains($value): bool
    {
        return $this->containsByEqualsCheck(
            $value,
            static fn (MappingComponentStructContract $a, MappingComponentStructContract $b): bool => $a->getPortalNodeKey()->equals($b->getPortalNodeKey())
                && $a->getEntityType()->equals($b->getEntityType())
                && $a->getExternalId() === $b->getExternalId()
        );
    }

    /**
     * @psalm-return class-string<DatasetEntityContract>[]
     *
     * @return string[]
     */
    public function getEntityTypes(): array
    {
        $entityTypes = (new EntityTypeCollection($this->map(
            static fn (MappingComponentStructContract $mapping): EntityType => $mapping->getEntityType()
        )))->asUnique()->asArray();

        return \array_map(static fn (EntityType $type): string => (string) $type, $entityTypes);
    }

    public function getPortalNodeKeys(): PortalNodeKeyCollection
    {
        return (new PortalNodeKeyCollection($this->map(
            static fn (MappingComponentStructContract $mapping): PortalNodeKeyInterface => $mapping->getPortalNodeKey()
        )))->asUnique();
    }

    /**
     * @return string[]
     */
    public function getExternalIds(): array
    {
        return (new StringCollection($this->map(
            static fn (MappingComponentStructContract $mapping): string => $mapping->getExternalId()
        )))->asUnique()->asArray();
    }

    public function filterByEntityType(EntityType $entityType): static
    {
        return $this->filter(
            static fn (MappingComponentStructContract $mc): bool => $mc->getEntityType()->equals($entityType)
        );
    }

    public function filterByPortalNodeKey(PortalNodeKeyInterface $portalNodeKey): static
    {
        return $this->filter(
            static fn (MappingComponentStructContract $mc): bool => $mc->getPortalNodeKey()->equals($portalNodeKey)
        );
    }

    protected function getT(): string
    {
        return MappingComponentStructContract::class;
    }
}
