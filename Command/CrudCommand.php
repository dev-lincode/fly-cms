<?php

namespace Lincode\Fly\Bundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Lincode\Fly\Bundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class CrudCommand extends GenerateDoctrineCrudCommand
{
    protected $generator;

    protected function configure()
    {
        parent::configure();

        $this->setName('fly:generate:crud');
        $this->setDescription('Generate CMS CRUD!');
    }

    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs(__DIR__ . '/../Resources/views/Generator/');
        }

        return $this->generator;
    }

    protected function createGenerator($bundle = NULL)
    {
        return new DoctrineCrudGenerator($this->getContainer()->get('filesystem'), __DIR__ . '/../Resources/views/Generator/');
    }
}
