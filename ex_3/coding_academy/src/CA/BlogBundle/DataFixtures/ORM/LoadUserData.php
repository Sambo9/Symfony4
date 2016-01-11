<?php
namespace CA\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CA\BlogBundle\Entity\Users;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
  /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setUsername('Martin');
        $plainPassword = 'platypus';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
        $user->setRoles(array('ROLE_BLOGGER'));
        $user->setMail('martin@coding.eu');

        $user2 = new Users();
        $user2->setUsername('Gecko');
        $plainPassword = 'coding';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user2, $plainPassword);
        $user2->setPassword($encoded);
        $user2->setRoles(array('ROLE_ADMIN'));
        $user2->setMail('gecko@coding.eu');

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}

 ?>
