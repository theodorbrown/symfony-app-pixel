<?php

namespace App\Controller;

use App\Address\AddressApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController {

    /**
     * @Route("/")
     */
    public function home(): Response {
        return $this->render("app/home.html.twig");
    }

    #[Route('search-address', name: 'searchaddress')]
    public function searchAddress(Request $req, AddressApiInterface $ai): Response{

        return new JsonResponse($ai->searchAddress($req->get('search', '')));
    }
}