<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        $response = $noteRepository->findAll();
        return new JsonResponse($response);
    }

    #[Route('/add', name: 'note_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $note = new Note();
        $note->setTitle($request->get('title'));
        $note->setText($request->get('text'));
        $note->setCreatedTime(new \DateTime() );

        $this->getDoctrine()->getManager()->persist($note);
        $this->getDoctrine()->getManager()->flush();


        return new JsonResponse(['message' => 'Note created successfully!']);
    }

    #[Route('/{id}', name: 'note_show', methods: ['GET', 'PUT', 'DELETE'])]
    public function show( Request $request, Note $note): Response
    {
        switch ( $request->getMethod()) {
            case 'GET':
                return new JsonResponse($note);
                break;
            case 'PUT':
                $note->setTitle($request->get('title'));
                $note->setText($request->get('text'));
                $this->getDoctrine()->getManager()->persist($note);
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(['message' => 'Note updated successfully.']);
                break;
            case 'DELETE':
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($note);
                $entityManager->flush();

                return new JsonResponse(['message' => 'Note deleted successfully.']);
                break;
            default:
            return new JsonResponse(['message' => 'Unknown method.']);
        }
    }
}
