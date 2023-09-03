<?php

declare(strict_types=1);

namespace Game\Utils;

use Symfony\Component\Yaml\Yaml;

/**
 * TODO all this stuff should live in memcache/redis or other persistent caching mechanism. Index precompiled. To avoid iterating over data.
 */
abstract class AbstractDataAccessor
{
    abstract protected function getDataName(): string;

    /**
     * @return array<mixed>
     */
    protected function getData(): array
    {
        $dataFile = sprintf('%s/data/%s.yaml', PROJECT_ROOT, $this->getDataName());

        $result = Yaml::parseFile($dataFile);

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }
}
