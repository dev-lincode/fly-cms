<?php
namespace Lincode\Fly\Bundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lincode\Fly\Bundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUser implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {
    private $container;

    public function load(ObjectManager $em) {
        $user = new User();
        $user->setName('Admin');
        $user->setEmail('admin@admin.com.br');
        $user->setPassword($this->encondePassword($user,'123'));
        $em->persist($user);
        $em->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    private function encondePassword($user, $plainPassword){
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }
}