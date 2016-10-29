<?php

namespace Lincode\Fly\Bundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand;
use Lincode\Fly\Bundle\Generator\BundleGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BundleCommand extends GenerateBundleCommand
{
    protected $generator;

    protected function configure()
    {
        parent::configure();

        $this->setName('fly:generate:bundle');
        $this->setDescription('Generate CMS Bundle!');
    }

    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs(__DIR__ . '/../Resources/views/Generator/');
        }

        return $this->generator;
    }

    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer()->get('filesystem'));
    }
}
