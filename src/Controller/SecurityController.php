<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryChoiceType;
use App\Form\CategoryType;
use App\Form\UserAddFormType;
use App\Form\UserEditByUserType;
use App\Repository\ForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
       
        return $this->render('security/login.html.twig', ['error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
	
	/**
	 * @Route("/admin/user", name="user_admin")
	 */
	public function admin(): Response
    {
		$users = $this->getDoctrine()
				->getRepository(User::class)
				->findAll();
		
	    return $this->render('security/admin.html.twig',
				[
					'users' => $users
				]
				);		
	}

    /**
     * @Route("/admin/user/add", name="user_add")
     */
	public function addUser(UserPasswordEncoderInterface $passwordEncoder,Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserAddFormType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
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
	 */
	public function delete(int $id): RedirectResponse
    {
		$user = $this->getDoctrine()
				->getRepository(User::class)
				->find($id);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($user);
		$entityManager->flush();

		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('user_admin');

	}

    /**
	 * @Route("/admin/user/edit/{id}", name="user_admin_edit")
	 */
	public function edit(Request $request, int $id): Response
    {
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

			$this->addFlash("success", "edit.success");
		}
        return $this->render('security/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
	}

    /**
     * @Route ("/user", name="user_edit_by_user")
     */
	public function editByuser(MailerInterface $mailer,Request $request, UserPasswordEncoderInterface
    $passwordEncoder, ForumRepository $forumRepository)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserEditByUserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if ($user->getPlainPassword() !== null) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new Email())
                ->from('accounts@agora.com')
                ->to($user->getEmail())
                ->subject("TEST EDIT")
                #->htmlTemplate('email/report.html.twig')
                #give a link with a random password. Link will be something like public/setPw/userid
                ->text("Bonjour ".$user->getUsername());
            if ($user->getIsAllowedEmails())
            {
                $mailer->send($email);
            }

            $this->addFlash("success", "edit.success");
        }
        #TODO: Envoyer les forums , puis les reponses dans deux var
        $forums = $forumRepository->findBy(['user'=>$user]);

        return $this->render('security/edit-by-user.html.twig', [
            'userForm' => $form->createView(),
            'forums' => $forums,
        ]);

    }

    /**
	 * @Route("/register", name="app_register")
	 */
	public function register(MailerInterface $mailer,Request $request, UserPasswordEncoderInterface $passwordEncoder,
                              GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsAllowedEmails(false);
            #$strings = ['d','2','$','@','D',0,3,8,6,1,2,'!'];
            #$random = rand(8000,15000).$strings[rand(0,10)].rand(100,500).$strings[rand(0,10)].rand(51,9531);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
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
     */
    public function userEditCategories(Request $request,int $id): Response
    {
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
     */
    public function userAddCategory(int $id, int $categoryId)
    {

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
     */
    public function userRemoveCategory(int $id, int $categoryId)
    {
        #TODO: Empecher l'acces a une page erreur en changeant l'URL ?
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
