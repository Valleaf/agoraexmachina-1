<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Notification;
use App\Entity\Report;
use App\Entity\Workshop;
use App\Entity\Proposal;
use App\Entity\Theme;
use App\Form\ForumType;
use App\Form\ForumAnswerType;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class ForumController Cette classe gère les forums et réponses. Leur visualisation, ajout , suppression,
 * modification et signalisation
 * @package App\Controller
 */
class ForumController extends AbstractController
{

    /**
     * @Route("/{slug}/workshop/{workshop}/forum", name="forum_index", methods={"GET"})
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Workshop $workshop Atelier choisi
     * @return Response Fonction qui affiche les forums d'un atelier
     */
    public function index(Request $request, string $slug, Workshop $workshop): Response
    {
        return $this->render('forum/index.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
            'workshop' => $workshop,
            'forums' => $this->getDoctrine()->getRepository(Forum::class)->FindBy(['workshop' => $workshop]),
        ]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/add", name="forum_add", methods={"GET", "POST"})
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Proposal $proposal Proposition choisie
     * @param Workshop $workshop Atelier choisi
     * @return Response Fonction qui ajoute un forum à une proposition
     */
    public function add(Request $request, string $slug, Proposal $proposal, Workshop $workshop): Response
    {
        # Crée un forum et le suite avec la requête
        $forum = new Forum();
        $forum->setUser($this->getUser());
        $forum->setProposal($proposal);
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($forum);
            $entityManager->flush();

            # Rediriger l'utilisateur vers l'index des forums de la proposition avec un message de succès
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
        }

        return $this->render('forum/add.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
            'workshop' => $proposal->getWorkshop(),
            'proposal' => $proposal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/edit/{forum}", name="forum_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Workshop $workshop Atelier choisi
     * @param Proposal $proposal Proposition choisie
     * @param Forum $forum Forum choisi
     * @return Response Fonction qui permet de modifier un forum
     */
    public function edit(Request $request, string $slug, Workshop $workshop, Proposal $proposal, Forum $forum): Response
    {
        # Création du formulaire et suivi avec la requête
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            # Rediriger l'utilisateur vers l'index des forums de la proposition avec un message de succès
            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
        }

        return $this->render('forum/edit.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
            'workshop' => $workshop,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}/forum/delete/{forum}", name="forum_delete", methods={"GET"})
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Workshop $workshop Atelier choisi
     * @param Proposal $proposal Proposition choisie
     * @param Forum $forum Forum choisi
     * @return Response Fonction qui permet de supprimer un forum
     */
    public function delete(Request $request, string $slug, Workshop $workshop, Proposal $proposal, Forum $forum): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($forum->getParentForum() != null)
        {
            $forum->getParentForum()->removeForum($forum);
        }
        $entityManager->remove($forum);
        $entityManager->flush();

        # Rediriger l'utilisateur vers l'index des forums de la proposition avec un message de succès
        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $workshop->getId(), 'proposal' =>
            $proposal->getId()]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/forum/answer/{forum}", name="forum_answer", methods={"GET", "POST"})
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Proposal $proposal Proposition choisie
     * @param Workshop $workshop Atelier choisi
     * @param Forum $forum Forum choisi parent
     * @return Response Fonction qui permet de répondre à un forum
     */
    public function answer(Request $request, string $slug, Proposal $proposal, Workshop $workshop, Forum $forum): Response
    {
        #TODO: Changer l'interface en reponse
        # Création d'un forum avec attribution du créateur et du forum parent
        $answer = new Forum();
        $answer->setUser($this->getUser());
        $answer->setProposal($proposal);
        $answer->setParentForum($forum);
        # Création du formulaire avec suivi de la requête
        $form = $this->createForm(ForumAnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            $entityManager->flush();

            # Envoi de notification à l'auteur du forum parent pour le prévenir d'une réponse
            $notification = $forum->getUser()->prepareNotification('Réponse : ' . $answer->getName());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();

            # Rediriger l'utilisateur vers l'index des forums de la proposition avec un message de succès
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);
        }

        return $this->render('forum/answer.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
            'workshop' => $proposal->getWorkshop(),
            'proposal' => $proposal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}/forum/report/{forum}", name="forum_report", methods={"GET"})
     * @param MailerInterface $mailer Permet l'envoi d'email
     * @param Request $request
     * @param string $slug Partie de l'URL avec le thème et l'atelier
     * @param Proposal $proposal Proposition choisie
     * @param Workshop $workshop Atelier choisi
     * @param Forum $forum Forum choisi
     * @return Response Fonction qui permet de signaler un forum aux administrateurs restreints et modérateur de la
     * catégorie. Disponible seulement pour les modérateurs et supérieurs
     * @throws TransportExceptionInterface Si erreur d'envoi d'email
     */
    public function report(MailerInterface $mailer, Request $request, string $slug, Proposal $proposal, Workshop $workshop,
                           Forum $forum):
    Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $workshop->getTheme()->getCategory()->getUsers();
        foreach ($users as $user) {
            # Cherche les administrateurs restreints et modérateurs des catégories correspondantes pour les prévenir
            if (in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN_RESTRICTED', $user->getRoles())) {
                $email = (new Email())
                    ->from($this->getUser()->getEmail())
                    ->to($user->getEmail())
                    ->subject('Message signalé ' . $forum->getName())
                    #->htmlTemplate('email/report.html.twig')
                    ->text($forum->getDescription());
                if ($user->getIsAllowedEmails()) {
                    try {
                        $mailer->send($email);
                    } catch (TransportException $e) {
                        #TODO: handle error
                    }
                }
                # Envoi de notification aux administrateurs et modérateurs concernés
                $notification = $user->prepareNotification('Message signalé ' . $forum->getName() . " : "
                    . $forum->getDescription());
                $entityManager->persist($notification);


            }
        }

        # Creation d'un Report et envoi dans la BDD.
        $entityManager->persist($forum->createReport());

        $entityManager->flush();
        $this->addFlash("success", "report.success");
        return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $proposal->getWorkshop()->getId(), 'proposal' => $proposal->getId()]);

    }

    #TODO: Faire une fonction show one forum avec un affichage modal / fonction avec en parametre juste l'id du forum!!

    /**
     * @Route("/forum/show/{forumId}",defaults={"forumId"=1}, name="forum_show", methods={"GET"})
     * Cette fonction permet de voir un seul forum. C'est un embedded Controller qui appelle une template apres a
     * voir été appelé dans une template
     */
    public function showForum(int $forumId=1): Response
    {
        #TODO: Limiter l'acces aux utilisateurs ayant acces a la categorie
        $forum = $this->getDoctrine()->getRepository(Forum::class)->find($forumId);
        if ($forum!=null) {
            return $this->render('forum/show.html.twig', [
                'forum' => $forum,
            ]);
        }
        else return $this->redirectToRoute('homepage');
    }


}