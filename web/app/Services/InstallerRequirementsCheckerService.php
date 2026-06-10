<?php

namespace App\Services;

class InstallerRequirementsCheckerService
{
    private string $_minPhpVersion = '7.0.0';

    public function check(array $requirements): array
    {
        $results = [];
        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                case 'php':
                    foreach ($requirement as $phpRequirement) {
                        $results['requirements'][$type][$phpRequirement] = true;

                        if (!extension_loaded($phpRequirement)) {
                            $results['requirements'][$type][$phpRequirement] = false;
                            $results['errors'] = true;
                        }
                    }
                    break;
                case 'apache':
                    foreach ($requirement as $serverRequirement) {
                        if (function_exists('apache_get_modules')) {
                            $results['requirements'][$type][$serverRequirement] = true;

                            if (!in_array($requirement, apache_get_modules())) {
                                $results['requirements'][$type][$serverRequirement] = false;
                                $results['errors'] = true;
                            }
                        }
                    }
                    break;
            }
        }
        return $results;
    }

    public function checkPhpVersion(string $minPhpVersion = null): array
    {
        $minVersionPhp     = $minPhpVersion;
        $currentPhpVersion = $this->getPhpVersionInfo();
        $supported         = false;

        if ($minPhpVersion == null) {
            $minVersionPhp = $this->getMinPhpVersion();
        }

        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }

        return [
            'full'      => $currentPhpVersion['full'],
            'current'   => $currentPhpVersion['version'],
            'minimum'   => $minVersionPhp,
            'supported' => $supported,
        ];
    }

    private static function getPhpVersionInfo(): array
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full'    => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

    protected function getMinPhpVersion(): string
    {
        return $this->_minPhpVersion;
    }
}
