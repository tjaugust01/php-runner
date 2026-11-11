<?php
declare(strict_types=1);

namespace SetupRunner\Config;

use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{

    public function load(string $configPath): array
    {
        if (!file_exists($configPath)) {
            throw new \InvalidArgumentException("Konfigurationsdatei '{$configPath}' konnte nicht gefunden werden.");
        }
        $file = file_get_contents($configPath);
        return Yaml::parse($file) ?? [];
    }
}