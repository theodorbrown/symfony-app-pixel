<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: '')]

class ApiController extends AbstractController
{
    #[Route('/game.{_format}', defaults:['_format'=>'json'], name: '')]

    public  function game(string $_format, Request $req, GameRepository $gameRepository, SerializerInterface $serializer): Response {
        $page = $req->get('p',1);
        $itemCount = 40;

        $entities = $gameRepository->findPagination($page, $itemCount);

        return new Response($serializer->serialize($entities, $_format));
    }
}
