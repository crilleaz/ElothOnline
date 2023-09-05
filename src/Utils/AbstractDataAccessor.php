<?php

declare(strict_types=1);

namespace Game\Utils;

use Symfony\Component\Yaml\Yaml;

abstract class AbstractDataAccessor
{
    /**
     * @var array<mixed>
     */
    private array $cache = [];

    abstract protected function getDataName(): string;

    /**
     * @return array<mixed>
     */
    protected function getData(): array
    {
        if ($this->cache === []) {
            $dataFile = sprintf('%s/data/%s.yaml', PROJECT_ROOT, $this->getDataName());
            $result   = Yaml::parseFile($dataFile);
            if (!is_array($result)) {
                return [];
            }

            $this->cache = $result;
        }

        return $this->cache;
    }
}
