<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\SpaceDocument;
use App\Form\SpaceDocumentType;
use App\Repository\SpaceDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/space/document")
 */
class SpaceDocumentController extends AbstractController
{
    /**
     * @Route("/", name="app_space_document_index", methods={"GET"})
     */
    public function index(SpaceDocumentRepository $spaceDocumentRepository): Response
    {
        return $this->render('space_document/index.html.twig', [
            'space_documents' => $spaceDocumentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_space_document_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SpaceDocumentRepository $spaceDocumentRepository): Response
    {
        $spaceDocument = new SpaceDocument();
        $form = $this->createForm(SpaceDocumentType::class, $spaceDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            self::uploadImage($form, $spaceDocument);
            $spaceDocumentRepository->add($spaceDocument);
            return $this->redirectToRoute('app_space_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('space_document/new.html.twig', [
            'space_document' => $spaceDocument,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_space_document_show", methods={"GET"})
     */
    public function show(SpaceDocument $spaceDocument): Response
    {
        return $this->render('space_document/show.html.twig', [
            'space_document' => $spaceDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_space_document_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, SpaceDocument $spaceDocument, SpaceDocumentRepository $spaceDocumentRepository): Response
    {
        $form = $this->createForm(SpaceDocumentType::class, $spaceDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spaceDocumentRepository->add($spaceDocument);
            return $this->redirectToRoute('app_space_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('space_document/edit.html.twig', [
            'space_document' => $spaceDocument,
            'form' => $form,
        ]);
    }

    public function uploadImage($form, $article)
    {
        $images = $form->get('documents')->getData();
        foreach($images as $image){
            $file = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $this->getParameter('documents_directory'),
                $file
            );
            
            $img = new Document();
            $img->setName($file);
            $article->addDocument($img);
        }
    }

    /**
     * @Route("/{id}", name="app_space_document_delete", methods={"POST"})
     */
    public function delete(Request $request, SpaceDocument $spaceDocument, SpaceDocumentRepository $spaceDocumentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$spaceDocument->getId(), $request->request->get('_token'))) {
            $spaceDocumentRepository->remove($spaceDocument);
        }

        return $this->redirectToRoute('app_space_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
