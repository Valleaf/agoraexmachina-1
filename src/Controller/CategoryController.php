<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Repository\RequestRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @package App\Controller
 * Cette classe permet l'affichage des catégories
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("admin/category", name="category_admin",methods={"GET"})
     * @param CategoryRepository $categoryRepository Le répertoire des catégories dans la BDD
     * @return Response Page affichant de toutes les catégories dans la page d'administration
     */
    public function admin(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/admin.html.twig', [
            'categories' => $categoryRepository->findAll(),
            #Toutes les catégories depuis la BDD
        ]);
    }

    /**
     * @Route("/admin/category/add", name="category_add")
     * @param Request $request La requête s'occupant de la gestion du formulaire
     * @return Response Page permettant d'ajouter une catégorie à la BDD
     */
    public function add(Request $request): Response
    {
        $category = new Category();
        #Creation du formulaire à partir de CategoryType
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Pour chaque administrateur, on l'ajoute a la catégorie
            $this->addAllAdminsToCategory($category);
            #On sauvegarde la catégorie dans la BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);

            #Si il y a des utilisateurs choisis lors de la création de la catégorie, on les ajoute un par un à la
            # catégorie
            if ($category->getUsers() != null) {
                $users = $category->getUsers();
                foreach ($users as $user) {
                    $user->addCategory($category);
                }
            }

            $entityManager->flush();
            #Ajout d'un message flash indiquant le succès de l'opération
            $this->addFlash("success", "add.success");
            #Redirection vers la fonction admin de CategoryController; l'index des catégories
            return $this->redirectToRoute('category_admin');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            #form : Le formulaire crée précédemment
            #category : la category que l'on veut ajouter
        ]);
    }

    /**
     * @Route("/admin/category/edit/{category}", name="category_edit", methods={"GET", "POST"})
     * @param Request $request La requête s'occupant de la gestion du formulaire
     * @param Category $category La catégorie à modifier
     * @return Response Une page permettant de modifier la catégorie en paramètre
     */
    public function edit(Request $request, Category $category): Response
    {
        #Création du formulaire gérant la catégorie grâce à CategoryType
        $usersBefore = $category->getUsers();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #TODO: Permettre d'enlever des utilisateurs des categories, peut-être comparer les utilisateurs dans
            # getUsers et la liste d'utilisateurs globale?
            # On enlève tous les utilisateurs de la catégorie pour ne remettre que ceux sélectionnés et ne pas
            # pouvoir selectionner les admins
            # if($usersBefore->count() != 0){
            #     foreach ($usersBefore as $user) {
            #         $category->removeUser($user);
            #         $this->getDoctrine()->getManager()->persist($user);
            #     }
            # }

            #Si il y a des utilisateurs choisis , on les ajoute un par un à la
            # catégorie
            if ($category->getUsers() != null) {
                $users = $category->getUsers();
                foreach ($users as $user) {
                    $user->addCategory($category);
                    $this->getDoctrine()->getManager()->persist($user);
                }
            }

            # Pour chaque administrateur, on l'ajoute a la categorie
            $this->addAllAdminsToCategory($category);

            #Persistence de la catégorie dans la BDD
            $this->getDoctrine()->getManager()->persist($category);
            $this->getDoctrine()->getManager()->flush();

            #Ajout d'un message flash indiquant le succès de l'opération
            $this->addFlash("success", "edit.success");
            #Renvoi vers la page permettant de modifier cette catégorie.
            return $this->redirectToRoute("category_edit", ["category" => $category->getId()]);
        }
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            #form : Le formulaire crée précédemment
            #category : la category que l'on veut modifier
        ]);
    }


    /**
     * @Route("/admin/category/delete/{category}", name="category_delete")
     * @param Category $category La catégorie que l'on veut supprimer
     * @return Response Une suppression de la catégorie en paramètre et un renvoi vers l'index des catégories
     */
    public function delete(Category $category): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        #Ajout d'un message flash indiquant le succès de l'opération et retour a l'index des catégories
        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('category_admin');
    }

    /**
     * @Route("/category/request",name="category_request")
     * @param CategoryRepository $categoryRepository Les catégories disponibles en BDD
     * @return Response Une page affichant toutes les catégories et permettqnt de faire des reauêtes pour rejoindre
     * une catégorie
     */
    public function requestCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/request.html.twig', [
            'categories' => $categoryRepository->findAll(),
            #Toutes les catégories en BDD
        ]);
    }

    /**
     * @Route("/category/request/{id}",name="category_send_request")
     * @param MailerInterface $mailer Le systeme permettant l'envoi d'emails
     * @param CategoryRepository $categoryRepository Les catégories disponibles en BDD
     * @param int $id L'id de la catégorie choisie
     * @return Response Cette fonction permet d'envoyer une requête à l'équipe de modération et d'administrateurs
     * restreints pour rejoindre une catégorie.
     */
    public function sendRequestForCategory(MailerInterface $mailer, CategoryRepository $categoryRepository, int $id): Response
    {

        $category = $categoryRepository->find($id);
        #Si l'utilisateur est déjà dans la catégorie, on le renvoi dans l'index des catégories
        if ($this->getUser()->getCategories()->contains($category)) {
            $this->addFlash("warning", "already.part-of-category");

            return $this->render('category/request.html.twig', [
                'categories' => $categoryRepository->findAll(),
            ]);
        }

        #TODO: then check if request already exists et qu'elle ne soit pas done
        #On recupere tous les utilisateurs de la catégorie
        $users = $category->getUsers();
        #Pour chaque utilisateur, on cherche les modérateurs et administrateurs restreints pour leur envoyer une
        # requête de demande de catégorie
        foreach ($users as $user) {
            if (in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN_RESTRICTED', $user->getRoles())) {
                #Envoi d'email à tous ces utilisateurs
                $email = (new Email())
                    ->from($this->getUser()->getEmail())
                    ->to($user->getEmail())
                    ->subject('Requête pour rejoindre la catégorie ' . $category->getName())
                    #->htmlTemplate('email/report.html.twig')
                    ->text("L'utilisateur " . $this->getUser()->getUsername() . " a demandé a rejoindre la catégorie "
                        . $category->getName());
                #Envoi seulement si ils autorisent les emails
                if ($user->getIsAllowedEmails()) {
                    try {
                        $mailer->send($email);
                    } catch (TransportExceptionInterface $e) {
                        #Si erreur dans l'envoi du mail, continue sans problème
                    }
                }
                #Envoi d'une notification a tous ces utilisateurs. A chaque notification est attachée une requête
                # pour suivre l'état de la demande
                $notification = $user->prepareNotification("L'utilisateur " . $this->getUser()->getUsername() . "a 
                demandé a rejoindre la catégorie " . $category->getName());
                $request = new \App\Entity\Request();
                $request->setUser($this->getUser());
                $request->setCategory($category);
                $request->addNotification($notification);
                $request->setIsDone(false);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($notification);
                $entityManager->flush();

            }
        }
        #Ajout d'un message flash indiquant le succès de l'opération et retour a l'index des requêtes des catégories
        $this->addFlash("success", "request.success");

        return $this->render('category/request.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/request/accept/{id}",name="category_accept_request")
     * @param int $id L'identifiant de la requête à accepter
     * @param RequestRepository $requestRepository Le répertoire des requêtes dans la BDD
     * @param TranslatorInterface $translator Permet d'avoir un texte traduit à partir des variables dans messages.xlf
     * @return Response Cette fonction accepte la requête puis retourne l'utilisateur sur sa page d'accueil des
     * notifications
     */
    public function acceptRequest(int $id, RequestRepository $requestRepository, TranslatorInterface $translator): Response
    {

        #TODO: Si la requete n'existe pas

        #TODO: Changer la notification, actuellement elle devient la meme des deux cotes

        #TODO: verification isdone et que le moderateur/admin correspond a la categorie

        #On récupère la requête, la catégorie en question et l'utilisateur demandant ainsi que le répondant
        $request = $requestRepository->find($id);
        $category = $request->getCategory();
        $user = $this->getUser();
        $userRequesting = $request->getUser();
        #Vérification que l'utilisateur sur la page soit bien un modératuer ou administrateur restreint, et que la
        # requête ne soit pas déjà isDone
        if (
            (in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN_RESTRICTED', $user->getRoles()))
            &&
            $user->getCategories()->contains($category)
            &&
            !$request->getIsDone()) {
            #On change le statut de la requête en isDone, et on ajoute l'utilisateur à la catégorie
            $request->setIsDone(true);
            $userRequesting->addCategory($category);
            #Ajout d'un message flash indiquant le succès de l'opération
            $this->addFlash("success", "request.accept");
            #On envoie une notification de l'acceptation à l'utilisateur ayant demandé à rejoindre la catégorie
            $notification = $userRequesting->prepareNotification($translator->trans('request.accepted') .
                $category->getName());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->persist($request);
            $entityManager->persist($userRequesting);
            $entityManager->flush();

            ##TODO: changer les autres requetes en isdone ou n'avoir qu'une seule requête envoyée et attachée à
            # toutes les même notifications
        }
        #Retour à la page des notifications
        return $this->forward('App\Controller\NotificationController::index', [
        ]);
    }

    /**
     * @Route("/category/request/deny/{id}",name="category_deny_request")
     * @param int $id L'identifiant de la requête à accepter
     * @param RequestRepository $requestRepository Le répertoire des requêtes dans la BDD
     * @param TranslatorInterface $translator Permet d'avoir un texte traduit à partir des variables dans messages.xlf
     * @return Response Cette fonction refuse la requête puis retourne l'utilisateur sur sa page d'accueil des
     * notifications
     */
    public function denyRequest(int $id, RequestRepository $requestRepository, TranslatorInterface $translator): Response
    {
#TODO: Si la requete n'existe pas
        #TODO: verification isdone et que le moderateur/admin correspond a la categorie

        #On récupère la requête, la catégorie en question et l'utilisateur demandant ainsi que le répondant
        $request = $requestRepository->find($id);
        $category = $request->getCategory();
        $user = $this->getUser();
        $userRequesting = $request->getUser();
        #Vérification que l'utilisateur sur la page soit bien un modératuer ou administrateur restreint, et que la
        # requête ne soit pas déjà isDone
        if (
            (in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN_RESTRICTED', $user->getRoles()))
            &&
            $user->getCategories()->contains($category)
            &&
            !$request->getIsDone()) {
            #On change le statut de la requête en isDone, et on ajoute l'utilisateur à la catégorie
            $request->setIsDone(true);
            #Ajout d'un message flash indiquant le succès de l'opération
            $this->addFlash("success", "request.denied");
            #On envoie une notification du refus à l'utilisateur ayant demandé à rejoindre la catégorie
            $notification = $userRequesting->prepareNotification($translator->trans('request.denied') .
                $category->getName());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();
        }
        #Retour à la page des notifications
        return $this->render('notification/index.html.twig', [
            'notifications' => $this->getDoctrine()->getRepository(Notification::class)->findAll(),
        ]);
    }

    /**
     * Cette fonction permet de recuperer tous les administrateurs dans la BDD et de les ajouter a une categorie
     * passee en paramètre.
     * @param Category $category
     */
    public function addAllAdminsToCategory(Category $category): void
    {
        $admins = $this->getDoctrine()->getRepository(User::class)->findAdmins();
        foreach ($admins as $admin) {
            $category->addUser($admin);
        }
    }

    /**
     * @Route ("/category/ajax/{theme}", defaults={"theme"=1}, name="category_ajax_theme")
     * @param Request $request
     * @param ForumRepository $forumRepository
     * @return JsonResponse
     */
    public function ajax_fetchCategoryFromTheme(Request $request, CategoryRepository $categoryRepository,
                                                ThemeRepository $themeRepository, string $theme)
    {
        if ($request->isXmlHttpRequest()) {


            $themes = $themeRepository->findBy(['name'=>$theme]);
            $jsonData = array();
            $idx = 0;
            foreach($themes as $theme) {
                # On envoie les donnes de chaque forums dans un json
                $temp = array(
                    'category' => $theme->getCategory()->getName(),
                );
                $jsonData[$idx++] = $temp;
            }

            return new JsonResponse($jsonData);
        }

    }

}
