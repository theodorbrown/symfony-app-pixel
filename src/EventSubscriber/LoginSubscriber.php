<?php

namespace App\EventSubscriber;

use App\Entity\Login;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface {

    //fait le lien entre les événements et ce qui va se passer (un evt? on appel cette fonction)
    public static function getSubscribedEvents(): array {
        return  [
            //On utilise constante : plus facile d'aller chercher
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess'
        ];
    }

    public function __construct(private EntityManagerInterface $entityManager) {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
        $user = $event->getAuthenticationToken()->getUser();

        $login = (new Login)
            ->setUser($user)
            ->setDate(new \DateTime)
        ;

        $this->entityManager->persist($login);
        $this->entityManager->flush();
    }
}