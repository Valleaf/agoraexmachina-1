<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\UserAddFormType;
use App\Form\UserEditByUserType;
use App\Repository\ForumRepository;
use App\Repository\ProposalRepository;
use App\Repository\WorkshopRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserEditFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SecurityController Cette classe gère les utilisateurs. Leurs connexions, déconnexions, inscriptions,
 * modifications etc..
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response Cette fonction gère la connexion de l'utilisateur et les erreurs éventuelles
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
       
        return $this->render('security/login.html.twig', ['error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * Cette fonction s'occupe de la déconnexion de l'utilisateur
     */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
	
	/**
	 * @Route("/admin/user", name="user_admin")
     * @return Response Cette fonction affiche une liste des utilisateurs , elle est destinée aux administrateurs
	 */
	public function admin(): Response
    {
        #Récupère tous les utilisateurs depuis la BDD
		$users = $this->getDoctrine()
				->getRepository(User::class)
				->findAll();

		#Envoi des utilisateurs à l'affichage
	    return $this->render('security/admin.html.twig',
				[
					'users' => $users
				]
				);		
	}

    /**
     * @Route("/admin/user/add", name="user_add")
     * @param UserPasswordEncoderInterface $passwordEncoder Encode le mot de passe de l'utilisateur
     * @param Request $request Requête gérant le formulaire d'inscription
     * @return Response Cette fonction permet d'ajouter un utilisateur à la BDD. Elle est destinée à un administrateur
     */
	public function addUser(UserPasswordEncoderInterface $passwordEncoder,Request $request): Response
    {
        # Crée un utilisateur et génère le formulaire nécessaire
        $user = new User();
        $form = $this->createForm(UserAddFormType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            #Encode le mot de passe donné dans le formulaire
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsAllowedEmails(false);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //TODO:: Utiliser messages
            $this->addFlash("success", "Création effectuée avec succès");
        }
        //TODO: regrouper les formulaires et les twig edit et add
        return $this->render('security/add.html.twig', [
            'userForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/admin/user/delete/{id}", name="user_delete")
     * @param int $id Utilisateur à supprimer
     * @return RedirectResponse Fonction qui supprime un utilisateur et redirige vers l'index des utilisateurs.
     * Destiné à un administrateur
     */
	public function delete(int $id): RedirectResponse
    {
        #On récupère l'utilisateur de la BDD
		$user = $this->getDoctrine()
				->getRepository(User::class)
				->find($id);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($user);
		$entityManager->flush();

		# Affichage d'un flash indiquant le succès et redirection
		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('user_admin');

	}

    /**
     * @Route("/admin/user/edit/{id}", name="user_admin_edit")
     * @param Request $request Gère le formulaire
     * @param int $id Utilisateur à modifier
     * @return Response Fonction qui permet de modifier un utilisateur. Destiné à un administrateur
     */
	public function edit(Request $request, int $id): Response
    {
        #On récupère l'utilisateur
		$user = $this->getDoctrine()
				->getRepository(User::class)
				->find($id);

		$form = $this->createForm(UserEditFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            # Affichage d'un flash indiquant le succès et redirection
			$this->addFlash("success", "edit.success");
		}
		# Création de la page et du formulaire
        return $this->render('security/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
	}

    /**
     * @Route ("/user", name="user_edit_by_user")
     * @param Request $request Gère le formulaire
     * @param UserPasswordEncoderInterface $passwordEncoder Permet d'encoder le mot de passe
     * @param ForumRepository $forumRepository Répertoire des forums 
     * @param ProposalRepository $proposalRepository Répertoire des propositions
     * @param WorkshopRepository $workshopRepository Répertoire des ateliers
     * @return Response Cette fonction affiche une page multi-fonction avec nagivation par onglet. On peut y voir son
     * profil, qu'on peut modifier, ainsi que ses forums ouverts, ses propositions crées et ses ateliers existant 
     * dans les thèmes auxquels on est souscrit .
     * @throws TransportExceptionInterface
     */
	public function editByUser(Request $request, UserPasswordEncoderInterface
    $passwordEncoder, ForumRepository $forumRepository, ProposalRepository $proposalRepository, WorkshopRepository
    $workshopRepository): Response
    {
        #On récupère l'utilisateur
        $user = $this->getUser();
        #On crée le formulaire pour permettre de modifier son profil
        $form = $this->createForm(UserEditByUserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            #Si le mot de passe est inclus dans la modification du profil, on l'encode
            if ($user->getPlainPassword() !== null) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            #Sauvegarde du nouveau profil
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute('homepage');
        }
        #TODO: verifier si on a besoin d'une fonction individuelle avec jointure pour éviter la multiplication de
        # requetes
        #On récupère les forums, propositions et ateliers nécessaires pour les envoyer à la template
        $forums = $forumRepository->findBy(['user'=>$user]);
        $proposals = $proposalRepository->findBy(['user'=>$user]);
        $workshops = $workshopRepository->findWorkshopsInCategories($user->getId());

        return $this->render('security/edit-by-user.html.twig', [
            'userForm' => $form->createView(),
            'forums' => $forums,
            'proposals' => $proposals,
            'workshops' => $workshops,
        ]);

    }

    /**
     * @Route("/register", name="app_register")
     * @param MailerInterface $mailer Permet d'envoyer un email
     * @param Request $request Gère le formulaire
     * @param UserPasswordEncoderInterface $passwordEncoder Encode le mot de passe
     * @param GuardAuthenticatorHandler $guardHandler S'occupe de l'enregistrement
     * @param LoginFormAuthenticator $authenticator S'occupe de la connexion
     * @return Response Une fonction permettant à un utilisateur de s'enregistrer
     * @throws TransportExceptionInterface
     */
	public function register(MailerInterface $mailer,Request $request, UserPasswordEncoderInterface $passwordEncoder,
                              GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        # On crée l'utilisateur et on crée le formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
#Si on enregistre un admin, il faut lui ajouter toutes les catégories.
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsAllowedEmails(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            #Email de confirmation TODO: envoi de mot de passe par email
            $email = (new Email())
                ->from('accounts@agora.com')
                ->to($user->getEmail())
                ->subject("Confirmation de l'inscription")
                #->htmlTemplate('email/report.html.twig')
                #give a link with a random password. Link will be something like public/setPw/userid
                ->text("Bonjour ".$user->getUsername());
            if ($user->getIsAllowedEmails())
            {
                $mailer->send($email);
            }            // do anything else you need here, like send an email
            # Retour à l'accueil en étant connecté
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/editCategory/{id}", name="user_edit_category")
     * @param Request $request Gère la modification
     * @param int $id Identifiant de l'utilisateur
     * @return Response Fonction permettant de modifier les catégories de l'utilisateur
     */
    public function userEditCategories(Request $request,int $id): Response
    {
        # On récupère l'utilisateur, et toutes les catégories existantes, pour les envoyer à la template
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('security/edit-category.html.twig', [
            'categories' => $categories,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/user/addCategory/{id}/{categoryId}", name="user_add_category")
     * @param int $id Identifiant de l'utilisateur
     * @param int $categoryId Identifiant de la catégorie
     * @return Response Fonction qui ajoute une catégorie à un utilisateur
     */
    public function userAddCategory(int $id, int $categoryId): Response
    {
        # On récupère l'utilisateur et la catégorie demandée et on l'ajoute
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        $user->addCategory($category);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('security/edit-category.html.twig', [
            'categories'=> $this->getDoctrine()->getRepository(Category::class)->findAll(),
            'user' => $user
        ]);

    }

    /**
     * @Route("/admin/user/removeCategory/{id}/{categoryId}", name="user_remove_category")
     * @param int $id Identifiant de l'utilisateur
     * @param int $categoryId Identifiant de la catégorie
     * @return Response Fonction qui enlève une catégorie à un utilisateur
     */
    public function userRemoveCategory(int $id, int $categoryId): Response
    {
        #TODO: Empecher l'acces a une page erreur en changeant l'URL ?
        # On récupère l'utilisateur et la catégorie demandée et on l'enlève
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $user->removeCategory($category);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('security/edit-category.html.twig', [
            'categories'=> $this->getDoctrine()->getRepository(Category::class)->findAll(),
            'user' => $user
        ]);

    }
    
    
}
