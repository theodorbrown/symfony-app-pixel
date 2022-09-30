<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Event\GameEvent;
use App\Event\GameEvents;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Définie un préfix pour toutes les routes de ce controller
 * 
 * @Route("/game")
 */

class GameController extends AbstractController {

    /**
     * @Route ("/")
     */
    public function list(Request $request, GameRepository $gameRepository): Response {

    /*
        //Si connecté
    if($this->getUser() instanceof User) {
        $entities = $gameRepository->findAll(); //tous les jeux
        $count = $gameRepository->count([]);
    //Sinon
    } else {
        $entities = $gameRepository->findEnabled();
        $count = $gameRepository->count(['enabled' => true]);
    }
     */
        $page = $request->get('p', 1);
        $itemCount = 1;

        $entities = $gameRepository->findPagination($page, $itemCount);
         
        $pageCount = \ceil($entities->count() / $itemCount);

        return $this->render("game/list.html.twig", [
            'entities' => $entities,
            'count' => $entities->count(),
            'pageCount' => $pageCount
        ]);

    }

    /**
     * @Route ("/new")
     * @IsGranted("ROLE_USER")
     */
    //EntityManagerInterface est un service. Objet que Symfony nous crée
    public function new(EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher): Response {

        //Autre manière d'obtenir EntityManager
        //$entityManager = $this.getDoctrine()->getManager();

        $entity = new Game;
        $entity->setUser($this->getUser());
        //création d'un nouveau formulaire en utilisant la classe GameType
        $form = $this->createForm(GameType ::class, $entity);

        //injection de la requête dans le formulaire pour récuperer les données POST : hydrater un objet
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity); //Prépare la requête : plusieurs persists sont possible
            $entityManager->flush(); //Execute la requête

            $eventDispatcher->dispatch(new GameEvent($entity), GameEvents::GAME_ADDED);

            //message de succès (pop up)
            $this->addFlash('success', $translator->trans('game.new.success', ['%game%' => $entity->getTitle()]));

            //redirection
            return $this->redirectToRoute("app_game_list");
        }

        return $this->render("game/new.html.twig", [
            'form' => $form->createView(), //envoie pour twig
        ]);
    }

    /**
     * Requirements indique la valeure attendue en paramètre
     * @Route("/{id}/edit", requirements={"id":"\d+"})
     */
    public function edit(Game $entity, Request $request, EntityManagerInterface $entityManagerInterface) : Response {

        $this->denyAccessUnlessGranted('EDIT', $entity);

        if(null === $entity->getUser()) {
            $entity->setUser($this->getUser());
        }

        $form = $this->createForm(GameType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->flush();//l'entité est déja enregistrée dans l'ORM pas besoin de faire un persist

            $this->addFlash('success', 'Le jeu a bien été modifié');

            return $this->redirectToRoute("app_game_list");
        }

        return $this->render("game/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }


    /**
     * Requirements indique la valeure attendue en paramètre
     * @Route("/{id}/delete", requirements={"id":"\d+"})
     */
    public function delete(Game $entity, Request $request, EntityManagerInterface $entityManagerInterface): Response {

        $this->denyAccessUnlessGranted('EDIT', $entity);

        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->get('token'))) {
            $entityManagerInterface->remove($entity);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_game_list');
        }

        return $this->render("game/delete.html.twig", [
            'entity' => $entity
        ]);
    }
}
