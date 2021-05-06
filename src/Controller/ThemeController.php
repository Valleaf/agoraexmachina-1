<?php
namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ThemeController extends AbstractController
{

	/**
	 * @Route("/admin/theme", name="theme_admin", methods={"GET"})
	 */
	public function admin(): Response
	{
		return $this->render('theme/admin.html.twig', [
					'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
		]);
	}

	/**
	 * @Route("/admin/theme/add", name="theme_add", methods={"GET", "POST"})
	 */
	public function add(Request $request): Response
	{
		$theme	 = new Theme();
		$form		 = $this->createForm(ThemeType::class, $theme);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($theme);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('theme_edit', ["theme" => $theme->getId()]);
		}

		return $this->render('theme/add.html.twig', [
					'form'		 => $form->createView()
		]);
	}

	/**
	 * @Route("/admin/theme/edit/{theme}", name="theme_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Theme $theme): Response
	{
		$form = $this->createForm(ThemeType::class, $theme);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
		    $workshops = $theme->getWorkshops();
		    if ($workshops != null)
		    {
		        foreach($workshops as $workshop)
                {
                    $workshop->setCategory($theme->getCategory());
                }
            }

			$this->getDoctrine()->getManager()->flush();
			
			$this->addFlash("success", "edit.success");
		}

		return $this->render('theme/edit.html.twig', [
					'form'		 => $form->createView(),
					'theme'	 => $theme
		]);
	}

	/**
	 * @Route("/admin/theme/delete/{theme}", name="theme_delete", methods={"GET"})
	 */
	public function delete(Request $request, Theme $theme): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($theme);
		$entityManager->flush();
		
		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('theme_admin');
	}

}