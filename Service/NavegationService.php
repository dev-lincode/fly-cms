<?php

namespace Lincode\Fly\Bundle\Service;

use Symfony\Component\Yaml\Yaml;

class NavegationService
{
    private $navegationFile = null;

    public function __construct($container)
    {
        if ($container->hasParameter('fly')) {
            $flyConfigs = $container->getParameter('fly');
            if (isset($flyConfigs['navegation'])) {
                $this->navegationFile = $flyConfigs['navegation'];
            }
        }
    }

    public function loadFile()
    {
        $navegation = null;
        if ($this->navegationFile) {
            $navegation = Yaml::parse(file_get_contents($this->navegationFile));
        }

        return $navegation;
    }
}
