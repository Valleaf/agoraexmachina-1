<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Keyword;
use App\Entity\Workshop;
use App\Entity\Theme;
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
		    $theme = $workshop->getTheme();
		    if ($theme->getCategory() != null) {
		        $workshop->setCategory($theme->getCategory());
            }

		    $keywords = $workshop->getKeytext();
		    $arr = explode(',',$keywords);
		    foreach ($arr as $key)
            {
                $keyword = new Keyword();
                $keyword->setName($key);
                $workshop->addKeyword($keyword);
            }


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

        #On verifie que l'admin restreint est enregistré a cette catégorie
        $admin = $this->getUser();
        $users = $workshop->getCategory()->getUsers();
        if (
        !(
            in_array('ROLE_ADMIN_RESTRICTED', $admin->getRoles())
            &&
            $users->contains($admin)
            ||
            in_array('ROLE_ADMIN', $admin->getRoles()))
        ) {
            $this->addFlash("warning", "edit.authorization");
            return $this->redirectToRoute('workshop_admin');
        }

		$form = $this->createForm(WorkshopType::class, $workshop);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $theme = $workshop->getTheme();
            if ($theme->getCategory() != null) {
                $workshop->setCategory($theme->getCategory());
            }
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
	 * @Route("/{slug}/{theme}", name="workshop_index", methods={"GET"})
	 */
	public function index(Request $request, string $slug, Theme $theme): Response
	{
		if ($request->query->get('search') != "")
			$workshops	 = $this->getDoctrine()->getRepository(Workshop::class)->searchBy(['theme' => $theme, 'name' => $request->query->get('search')]);
		else
			$workshops	 = $this->getDoctrine()->getRepository(Workshop::class)->findBy(['theme' => $theme]);

		return $this->render('workshop/index.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'theme'	 => $theme,
				'workshops'	 => $workshops,
		]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}", name="workshop_show", methods={"GET"})
	 */
	public function show(Request $request, string $slug, Workshop $workshop): Response
	{
		return $this->render('workshop/show.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $this->getDoctrine()->getRepository(Workshop::class)->findOneById($workshop),
		]);
	}

	/**
	 * @Route("/{slug}/{theme}/add", name="workshop_add_byuser", methods={"GET", "POST"})
	 */
	public function addByUser(Request $request, string $slug, Theme $theme)
	{

		$workshop	 = new Workshop();
		$workshop->setUser($this->security->getUser());
		
		$form		 = $this->createForm(WorkshopType::class, $workshop);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $theme = $workshop->getTheme();
            if ($theme->getCategory() != null) {
                $workshop->setCategory($theme->getCategory());
            }
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($workshop);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('workshop_index', ["theme" => $theme->getId(), "slug" => $slug]);
		}

		return $this->render('workshop/add.byuser.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'theme'	 => $theme,
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