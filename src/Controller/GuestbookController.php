<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Guestbook;
use App\Form\GuestbookFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Service\FileUploader;


class GuestbookController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    #[Route('/guestbook/list', name: 'app_guestbook_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Guestbook::class);

        $guestbooks = $repository->findAll();

        return $this->render('guestbook/list.html.twig', [
            'guestbooks' => $guestbooks
        ]);
    }

    #[Route('/guestbook/add', name: 'app_guestbook_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $guestbook = new Guestbook();
        $form = $this->createForm(GuestbookFormType::class, $guestbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $guestbook = $form->getData();

            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $guestbook->setImage($imageFileName);
                $guestbook->setType('image');
            } else {
                $guestbook->setType('text');   
            }
           
            $user = $this->security->getUser();
            $guestbook->setUser($user);
            $guestbook->setStatus('pending');
            $guestbook->setCreatedAt(new \DateTimeImmutable());
            $guestbook->setUpdatedAt(new \DateTimeImmutable());
            $guestbook->setUpdatedBy($user->getId());

            $entityManager->persist($guestbook);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Data sent for approval.'
            );

            return $this->redirectToRoute('homepage');
            
        }

        return $this->render('guestbook/add.html.twig', [
            'GuestbookForm' => $form->createView(),
        ]);
    }

    #[Route('/guestbook/view/{id}', name: 'app_guestbook_view')]
    public function view(ManagerRegistry $doctrine, int $id): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');        

        $repository = $doctrine->getRepository(Guestbook::class);
        $guestbook = $repository->find($id);

        if (!$guestbook) {
            throw $this->createNotFoundException('Record does not exist');
        }

        return $this->render('guestbook/view.html.twig', [
            'guestbook' => $guestbook
        ]);
    }

    #[Route('/guestbook/approve/{id}', name: 'app_guestbook_approve')]
    public function approve(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, int $id): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Guestbook::class);
        $guestbook = $repository->find($id);

        if (!$guestbook) {
            throw $this->createNotFoundException('Record does not exist');
        }

        $guestbook->setStatus('approved');

        $entityManager->persist($guestbook);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Data approved.'
        );

        return $this->redirectToRoute('app_guestbook_list');
    }

    #[Route('/guestbook/delete/{id}', name: 'app_guestbook_delete')]
    public function delete(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, int $id): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Guestbook::class);
        $guestbook = $repository->find($id);

        if (!$guestbook) {
            throw $this->createNotFoundException('Record does not exist');
        }

        $entityManager->remove($guestbook);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Data deleted.'
        );

        return $this->redirectToRoute('app_guestbook_list');
    }

    #[Route('/guestbook/edit/{id}', name: 'app_guestbook_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, FileUploader $fileUploader, int $id): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Guestbook::class);
        $guestbook = $repository->find($id);
        if (!$guestbook) {
            throw $this->createNotFoundException('Record does not exist');
        }

        $form = $this->createForm(GuestbookFormType::class, $guestbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $guestbook = $form->getData();

            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $guestbook->setImage($imageFileName);
                
            } 

            if (isset($imageFileName) || $guestbook->getImage()) {
                $guestbook->setType('image');
            } else {
                $guestbook->setType('text');
            }
           
            $user = $this->security->getUser();
            $guestbook->setUpdatedAt(new \DateTimeImmutable());
            $guestbook->setUpdatedBy($user->getId());

            $entityManager->persist($guestbook);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Data updated successfully.'
            );

            return $this->redirectToRoute('app_guestbook_list');
            
        }

        return $this->render('guestbook/edit.html.twig', [
            'GuestbookForm' => $form->createView(),
        ]);
    }
}
