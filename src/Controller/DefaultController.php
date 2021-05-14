<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Workshop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

	/**
	 * @Route("/", name="homepage", methods={"GET"});
	 */
	public function index()
	{
		return $this->render('index.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
		]);
	}



	/**
	 * @Route("/admin", name="admin", methods={"GET"});
	 */
	public function admin()
	{
		return $this->render('admin.html.twig', [
				'users'		 => $this->getDoctrine()->getRepository(User::class)->findAll(),
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
		]);
	}

}