<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;

interface CronjobContextInterface
{
    public function getPortal(): PortalContract;

    public function getConfig(): ?array;

    public function getCronjob(): CronjobInterface;
}