<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Support\Contract;

class DeepObjectIteratorContract
{
    /**
     * @var array<class-string, \ReflectionProperty[]>
     */
    private array $reflectionProperties = [];

    /**
     * @return iterable<int, object>
     */
    public function iterate(object|iterable $object): iterable
    {
        $toIterate = [$object];
        $alreadyChecked = [];

        do {
            $newIterables = [];

            /** @var mixed $iterable */
            foreach ($toIterate as $iterable) {
                if (\is_object($iterable)) {
                    $class = $iterable::class;

                    if (\in_array($iterable, $alreadyChecked[$class] ?? [], true)) {
                        continue;
                    }

                    $alreadyChecked[$class][] = $iterable;
                    yield $iterable;
                    $newIterables[] = \iterable_to_array($this->iterateProperties($iterable));
                } elseif (\is_iterable($iterable)) {
                    $newIterables[] = \iterable_to_array($this->iterateIterable($iterable));
                }
            }

            $toIterate = \array_merge([], ...\array_map('array_values', $newIterables));
        } while ($toIterate !== []);
    }

    private function iterateProperties(object $object): iterable
    {
        try {
            foreach ($this->getPropertiesAccessor($object) as $prop) {
                try {
                    $value = $prop->getValue($object);

                    if (\is_object($value) || \is_iterable($value) || (\is_array($value) && $value !== [])) {
                        yield $value;
                    }
                } catch (\Throwable) {
                }
            }
        } catch (\Throwable) {
        }
    }

    private function iterateIterable(iterable $iterable): iterable
    {
        yield from $iterable;
    }

    /**
     * @throws \ReflectionException
     *
     * @return \ReflectionProperty[]
     */
    private function getPropertiesAccessor(object $object): array
    {
        $class = $object::class;
        $result = $this->reflectionProperties[$class] ?? null;

        if (\is_array($result)) {
            return $result;
        }

        $preResult = new \ReflectionClass($class);
        $result = [];

        foreach ($preResult->getProperties() as $property) {
            $property->setAccessible(true);
            $result[] = $property;
        }

        return $this->reflectionProperties[$class] = $result;
    }
}
