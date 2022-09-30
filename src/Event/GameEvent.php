<?php

namespace App\Event;

use App\Entity\Game;
use Symfony\Contracts\EventDispatcher\Event;

class GameEvent extends Event {
    
    /**
     * @var Game
     */

    public function __construct(private Game $game) {
    }

    /**
     * Get the value of game
     */ 
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * Set the value of game
     *
     * @return  self
     */ 
    public function setGame($game): self
    {
        $this->game = $game;

        return $this;
    }
}
