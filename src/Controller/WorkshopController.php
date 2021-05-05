<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Workshop;
use App\Entity\Category;
use App\Form\DocumentType;
use App\Form\WorkshopDocumentsType;
use App\Form\WorkshopType;
use App\Repository\WorkshopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class WorkshopController extends AbstractController
{
	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	/**
	 * @Route("/admin/workshop", name="workshop_admin", methods={"GET"})
	 */
	public function admin(WorkshopRepository $workshopRepository): Response
	{
		return $this->render('workshop/admin.html.twig', [
				'workshops' => $workshopRepository->findAll(),
		]);
	}

	/**
	 * @Route("/admin/workshop/add", name="workshop_add")
	 */
	public function add(Request $request): Response
	{
		$workshop	 = new Workshop();
		$workshop->setUser($this->security->getUser());
		
		$form		 = $this->createForm(WorkshopType::class, $workshop);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($workshop);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('workshop_edit', ["workshop" => $workshop->getId()]);
		}

		return $this->render('workshop/add.html.twig', [
				'form'		 => $form->createView(),
				'workshop'	 => $workshop,
		]);
	}

	/**
	 * @Route("/admin/workshop/edit/{workshop}", name="workshop_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Workshop $workshop): Response
	{
		$form = $this->createForm(WorkshopType::class, $workshop);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash("success", "edit.success");
			return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
		}

		return $this->render('workshop/edit.html.twig', [
				'form'		 => $form->createView(),
				'workshop'	 => $workshop,
		]);
	}

	/**
	 * @Route("/admin/workshop/delete/{workshop}", name="workshop_delete")
	 */
	public function delete(Request $request, Workshop $workshop): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($workshop);
		$entityManager->flush();

		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('workshop_admin');
	}

	/**
	 * @Route("/{slug}/{category}", name="workshop_index", methods={"GET"})
	 */
	public function index(Request $request, string $slug, Category $category): Response
	{
		if ($request->query->get('search') != "")
			$workshops	 = $this->getDoctrine()->getRepository(Workshop::class)->searchBy(['category' => $category, 'name' => $request->query->get('search')]);
		else
			$workshops	 = $this->getDoctrine()->getRepository(Workshop::class)->findBy(['category' => $category]);

		return $this->render('workshop/index.html.twig', [
				'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll(),
				'category'	 => $category,
				'workshops'	 => $workshops,
		]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}", name="workshop_show", methods={"GET"})
	 */
	public function show(Request $request, string $slug, Workshop $workshop): Response
	{
		return $this->render('workshop/show.html.twig', [
				'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $this->getDoctrine()->getRepository(Workshop::class)->findOneById($workshop),
		]);
	}

	/**
	 * @Route("/{slug}/{category}/add", name="workshop_add_byuser", methods={"GET", "POST"})
	 */
	public function addByUser(Request $request, string $slug, Category $category)
	{

		$workshop	 = new Workshop();
		$workshop->setUser($this->security->getUser());
		
		$form		 = $this->createForm(WorkshopType::class, $workshop);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($workshop);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('workshop_index', ["category" => $category->getId(), "slug" => $slug]);
		}

		return $this->render('workshop/add.byuser.html.twig', [
				'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll(),
				'category'	 => $category,
				'form'		 => $form->createView(),
				'workshop'	 => $workshop,
		]);
	}

    /**
     * @Route("/admin/workshop/addDocument/{workshop}", name="workshop_add_document")
     */
	public function addDocument(Request $request, Workshop $workshop): Response
    {
        #Si il y a deja plus de 4 documents, on redirige l'utilisateur vers la page edit
        $maxDocuments = 5;
        if ($workshop->getDocuments()->count()>=$maxDocuments)
        {
            $this->addFlash('warning','Il y a deja le nombre maximum de documents');
            return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
        }

        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $workshop->addDocument($document);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
        }

        return $this->render('workshop/add-document.html.twig', [
            'form'		 => $form->createView(),
            'workshop'	 => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/editDocument/{workshop}", name="workshop_edit_document")
     */
    public function editDocuments(Workshop $workshop): Response
    {
        return $this->render('workshop/edit-document.html.twig', [
            'workshop'	 => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/deleteDocument/{workshop}/{document}", name="workshop_delete_document")
     */
    public function deleteDocuments(Workshop $workshop,Document $document): Response
    {

        //TODO: Rajouter un renvoi si les id n'existent pas
        $workshop->removeDocument($document);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($workshop);
        $entityManager->flush();

        $this->addFlash("success", "delete.success");
        //TODO: Erreur en redirigeant vers les documents, redirige vers la page admin donc, probleme de parametre
        return $this->redirectToRoute('admin',[
        ]);
    }

}