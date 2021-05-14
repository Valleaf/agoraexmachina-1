<?php

namespace App\Controller;

use App\Entity\Delegation;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Workshop;
use App\Form\DelegationThemeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DelegationController extends AbstractController
{

	/**
	 * @Route("/user/delegation/", name="delegation_index", methods={"GET"})
	 */
	public function index(Request $request): Response
	{
		return $this->render('delegation/index.html.twig', [
				'delegationsFrom'	 => $this->getDoctrine()->getRepository(Delegation::class)->FindBy(
					[
						'userFrom' => $this->getUser(),
					]
				),
				'delegationsTo'		 => $this->getDoctrine()->getRepository(Delegation::class)->FindBy(
					[
						'userTo' => $this->getUser(),
					]
				),
		]);
	}

	/**
	 * @Route("/delegation/theme/{theme}/add", name="delegation_add_theme", methods={"GET", "POST"})
	 */
	public function addTheme(Request $request, Theme $theme, TranslatorInterface $translator): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$delegation = $entityManager->getRepository(Delegation::Class)->findOneBy(
			[
				'userFrom'	 => $this->getUser(),
				'theme'	 => $theme
		]);

		//case insert
		if (!$delegation)
			$delegation = new Delegation();


		$delegation->setUserFrom($this->getUser());
		$delegation->settheme($theme);

		$form = $this->createForm(DelegationThemeType::class, $delegation);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($delegation);
			$entityManager->flush();

            $notification = $delegation->getUserTo()->prepareNotification($translator->trans('delegation').' : '
                .$delegation->getTheme().' '.$translator->trans('from').' '
                .$this->getUser()->getUsername());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('delegation_index');
		}

		return $this->render('delegation/add.theme.html.twig', [
				'form' => $form->createView()
		]);
	}
	
	
	/**
	 * @Route("/delegation/workshop/{workshop}/add", name="delegation_add_workshop", methods={"GET", "POST"})
	 */
	public function addWorkshop(Request $request, Workshop $workshop): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$delegation = $entityManager->getRepository(Delegation::Class)->findOneBy(
			[
				'userFrom'	 => $this->getUser(),
				'workshop'	 => $workshop
		]);

		//case insert
		if (!$delegation)
			$delegation = new Delegation();


		$delegation->setUserFrom($this->getUser());
		$delegation->setWorkshop($workshop);

		$form = $this->createForm(DelegationWorkshopType::class, $delegation);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($delegation);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('delegation_index');
		}

		return $this->render('delegation/add.workshop.html.twig', [
				'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/user/delegation/delete/{delegation}", name="delegation_delete", methods={"GET"})
	 */
	public function delete(Request $request, Delegation $delegation, TranslatorInterface $translator): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($delegation);
		$entityManager->flush();

        $notification = $delegation->getUserTo()->prepareNotification($translator->trans('delegation.deleted')
            .' '.$delegation->getTheme().' '.$translator->trans('from')
            .' '.$this->getUser()->getUsername());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('delegation_index');
	}

}