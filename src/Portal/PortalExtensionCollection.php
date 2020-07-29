<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Support\AbstractCollection;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverCollection;

/**
 * @extends \Heptacom\HeptaConnect\Dataset\Base\Support\AbstractCollection<\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionInterface>
 */
class PortalExtensionCollection extends AbstractCollection
{
    public function getEmitterDecorators(): EmitterCollection
    {
        $result = new EmitterCollection();

        /** @var PortalExtensionInterface $extension */
        foreach ($this->getIterator() as $extension) {
            $result->push($extension->getEmitterDecorators()->getIterator());
        }

        return $result;
    }

    public function getExplorerDecorators(): ExplorerCollection
    {
        $result = new ExplorerCollection();

        /** @var PortalExtensionInterface $extension */
        foreach ($this->getIterator() as $extension) {
            $result->push($extension->getExplorerDecorators()->getIterator());
        }

        return $result;
    }

    public function getReceiverDecorators(): ReceiverCollection
    {
        $result = new ReceiverCollection();

        /** @var PortalExtensionInterface $extension */
        foreach ($this->getIterator() as $extension) {
            $result->push($extension->getReceiverDecorators()->getIterator());
        }

        return $result;
    }

    protected function isValidItem($item): bool
    {
        /* @phpstan-ignore-next-line treatPhpDocTypesAsCertain checks soft check but this is the hard check */
        return $item instanceof PortalExtensionInterface;
    }
}