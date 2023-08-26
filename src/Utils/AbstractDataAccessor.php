<?php
declare(strict_types=1);

namespace Game\Utils;

use Symfony\Component\Yaml\Yaml;

// TODO all this stuff should live in memcache/redis or other persistent caching mechanism. Index precompiled. To avoid iterating over data.
abstract class AbstractDataAccessor
{
    abstract protected function getDataName(): string;

    protected function getData(): array
    {
        $dataFile = sprintf('%s/data/%s.yaml', PROJECT_ROOT, $this->getDataName());

        return Yaml::parseFile($dataFile);
    }
}
