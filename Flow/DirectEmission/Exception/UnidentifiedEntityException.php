<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Flow\DirectEmission\Exception;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;

class UnidentifiedEntityException extends \Exception
{
    private DatasetEntityContract $entity;

    public function __construct(DatasetEntityContract $entity)
    {
        parent::__construct(\sprintf(
            'DirectEmissionFlow: Direct emission was attempted for an unidentified entity of type %s. It is missing its primary key.',
            $entity::class
        ));

        $this->entity = $entity;
    }

    public function getEntity(): DatasetEntityContract
    {
        return $this->entity;
    }
}
