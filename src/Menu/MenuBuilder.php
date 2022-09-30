<?php

// src/Menu/MenuBuilder.php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{

    /**
     * Add any other dependency you need...
     */
    public function __construct(private FactoryInterface $factory, private Security $security)
    {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Accueil', ['route' => 'app_app_home']);
        $menu->addChild('Jeux', ['route' => 'app_game_list']);
        $menu->addChild('Supports', ['route' => 'support_index']);
        $menu->addChild('Catégories', ['route' => 'category_index']);
        // ... add more children

        return $menu;
    }

    public function createUserMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        
        if($this->security->isGranted('ROLE_USER')) {
            $user = $this->security->getUser();
            $child = $menu->addChild($user->getEmail(), ['uri' => '#']);
            $child->addChild('Se déconnecter', ['route' => 'app_logout']);
        } else {
            $menu->addChild('Connexion', ['route' => 'app_login']);
            $menu->addChild('Inscription', ['route' => 'app_register', 'attributes' => [
                'class' => 'menu-register'
            ]]);
        }

        return $menu;
    }
}