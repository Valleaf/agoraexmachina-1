<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Workshop;
use App\Entity\Proposal;
use App\Entity\Theme;
use App\Form\ForumType;
use App\Form\ForumAnswerType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{

	/**
	 * @Route("/{slug}/workshop/{workshop}/forum", name="forum_index", methods={"GET"})
	 */
	public function index(Request $request, string $slug, Workshop $workshop): Response
	{
		return $this->render('forum/index.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $workshop,
				'forums'	 => $this->getDoctrine()->getRepository(Forum::class)->FindBy(['workshop' => $workshop]),
		]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/add", name="forum_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, string $slug, Proposal $proposal, Workshop $workshop): Response
	{
		$forum	 = new Forum();
		$forum->setUser($this->getUser());
		$forum->setProposal($proposal);
		$form	 = $this->createForm(ForumType::class, $forum);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($forum);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
		}

		return $this->render('forum/add.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $proposal->getWorkshop(),
				'proposal'	 => $proposal,
				'form'		 => $form->createView(),
		]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/edit/{forum}", name="forum_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, string $slug, Workshop $workshop, Proposal $proposal, Forum $forum): Response
	{
		$form = $this->createForm(ForumType::class, $forum);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash("success", "edit.success");
			return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
		}

		return $this->render('forum/edit.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $workshop,
				'form'		 => $form->createView(),
		]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}/forum/delete/{forum}", name="forum_delete", methods={"GET"})
	 */
	public function delete(Request $request, string $slug, Workshop $workshop, Proposal $proposal, Forum $forum): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($forum);
		$entityManager->flush();

		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
	}

	/**
	 * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/answer/{forum}", name="forum_answer", methods={"GET", "POST"})
	 */
	public function answer(Request $request, string $slug, Proposal $proposal, Workshop $workshop, Forum $forum): Response
	{
		$answer	 = new Forum();
		$answer->setUser($this->getUser());
		$answer->setProposal($proposal);
		$answer->setParentForum($forum);
		$form	 = $this->createForm(ForumAnswerType::class, $answer);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($answer);
			$entityManager->flush();

			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
		}

		return $this->render('forum/answer.html.twig', [
				'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
				'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
				'workshop'	 => $proposal->getWorkshop(),
				'proposal'	 => $proposal,
				'form'		 => $form->createView(),
		]);
	}
    /**
     * @Route("/{slug}/workshop/{workshop}/forum/report/{forum}", name="forum_report", methods={"GET"})
     */
	public function report(MailerInterface $mailer, Request $request, string $slug, Proposal $proposal, Workshop $workshop,
                           Forum
$forum):
Response
    {


        $users = $workshop->getCategory()->getUsers();
        foreach ($users as $user)
        {
            if (in_array('ROLE_ADMIN',$user->getRoles()) ||  in_array('ROLE_ADMIN_RESTRICTED',$user->getRoles()))
            {
                $email = (new Email())
                    ->from($this->getUser()->getEmail())
                    ->to($user->getEmail())
                    ->subject('Message signalé'.$forum->getName())
                   #->htmlTemplate('email/report.html.twig')
                    ->text($forum->getDescription());
                $mailer->send($email);
            }
        }

        $this->addFlash("success", "report.success");
        return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);

    }

}