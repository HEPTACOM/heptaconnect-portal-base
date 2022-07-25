<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\SubtypeClassStringContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;

/**
 * @extends SubtypeClassStringContract<PortalContract>
 */
final class PortalType extends SubtypeClassStringContract
{
    public function getExpectedSuperClassName(): string
    {
        return PortalContract::class;
    }
}
