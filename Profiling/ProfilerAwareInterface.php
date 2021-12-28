<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Portal\Base\Profiling;

interface ProfilerAwareInterface
{
    public function setProfiler(ProfilerContract $profiler): void;
}
