<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Guestbook;

class PageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Guestbook::class);

        $guestbooks = $repository->findBy([
            'status' => 'approved'
        ]);

        return $this->render('page/index.html.twig', [
            'guestbooks' => $guestbooks
        ]);
    }
}
