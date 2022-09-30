<?php

namespace App\EventSubscriber;

use App\Event\GameEvent;
use App\Event\GameEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class GameSubscriber implements EventSubscriberInterface {


    public static function getSubscribedEvents(): array {
        return [
            GameEvents::GAME_ADDED => 'onGameAdded',
        ];
    }

    public function __construct(private MailerInterface $mailer) {
    }

    public function onGameAdded(GameEvent $event): void {
        $game = $event->getGame();
        $email = (new Email())
            ->to(new Address('admin@pixel.fr'))
            ->subject("Nouveau jeu video dans la base" + $game->getTitle())
            ->html("Découvrez le de suite sur Adminer")
            ->from(new Address('notif@pixel.fr'))
        ;

        $this->mailer->send($email);
    }
}