<?php

namespace Lincode\Fly\Bundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineEntityCommand;

class EntityCommand extends GenerateDoctrineEntityCommand {

    protected function configure()
    {
        parent::configure();

        $this->setName('fly:generate:entity');
        $this->setDescription('Generate CMS Entity!');
    }

}