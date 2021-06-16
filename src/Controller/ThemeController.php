<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ThemeController Cette classe gère la gestion des Thèmes et leur affichage
 * @package App\Controller
 */
class ThemeController extends AbstractController
{

    /**
     * @Route("/admin/theme", name="theme_admin", methods={"GET"})
     * @return Response Une fonction qui affiche les thèmes pour l'administrateur
     */
    public function admin(MailerInterface $mailer): Response
    {
       #$user=$this->getUser();
       #$user->sendEmailToUser($mailer,$this->getParameter('app.smtp_email'),'TEST','Bonjour!');


        return $this->render('theme/admin.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
        ]);
    }

    /**
     * @Route("/admin/theme/add", name="theme_add", methods={"GET", "POST"})
     * @param Request $request Gère le formulaire
     * @return Response Une fonction qui permet d'ajouter un thème à la BDD
     */
    public function add(Request $request): Response
    {
        #On crée un thème et le formulaire nécessaire
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

            # Ajout d'un message flash indiquant le succès et redirection vers la page pour modifier le thème en
            # question
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('theme_edit', ["theme" => $theme->getId()]);
        }

        return $this->render('theme/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/theme/edit/{theme}", defaults={"theme"=1}, name="theme_edit", methods={"GET", "POST"})
     * @param Request $request Gère le formulaire
     * @param Theme $theme Thème à modifier
     * @return Response Fonction qui permet de modifier un thème
     */
    public function edit(Request $request, Theme $theme): Response
    {
        # On verifie que l'admin restreint est enregistré a cette catégorie ou que c'est un admin
        # Sinon on le redirige avec un message flash le prévenant qu'il n'a pas les droits
        $admin = $this->getUser();
        $users = $theme->getCategory()->getUsers();
        if (
            !(
                in_array('ROLE_ADMIN_RESTRICTED', $admin->getRoles())
                &&
                $users->contains($admin)
            ||
            in_array('ROLE_ADMIN', $admin->getRoles()))
        ) {

            $this->addFlash("warning", "edit.authorization");
            return $this->redirectToRoute('theme_admin');
        }

        #Création du formulaire à partir du thème et gestion via la requête
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "edit.success");
        }

        return $this->render('theme/edit.html.twig', [
            'form' => $form->createView(),
            'theme' => $theme
        ]);
    }

    /**
     * @Route("/admin/theme/delete/{theme}", name="theme_delete", methods={"GET"})
     * @param Request $request
     * @param Theme $theme Le thème à supprimer
     * @return Response Fonction qui supprime un thème
     */
    public function delete(Request $request, Theme $theme): Response
    {
        # On verifie que l'admin restreint est enregistré a cette catégorie
        # TODO accepter un administrateur
        $admin = $this->getUser();
        $users = $theme->getCategory()->getUsers();
        if (!(
            in_array('ROLE_ADMIN_RESTRICTED', $admin->getRoles())
            &&
            $users->contains($admin)
        )) {
            #Redirection si non-droit
            return $this->redirectToRoute('theme_admin');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($theme);
        $entityManager->flush();
        # Suppression du thème, redirection vers la page d'index des thèmes dans l'administration et affichage d'un
        # message indiquant le succès de l'opération
        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('theme_admin');
    }

}