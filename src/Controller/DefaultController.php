<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Website;
use App\Entity\Workshop;
use App\Form\RegistrationFormType;
use App\Form\WebsiteType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage", methods={"GET"});
     */
    public function index()
    {

        #Si aucun compte n'existe, rediriger vers une page pour creer un compte admin
        $numberOfAdmins = $this->getDoctrine()->getRepository(User::class)->findAdmins();
        $numberOfAdmins = count($numberOfAdmins);
        if ($numberOfAdmins == 0) {
            $entityManager = $this->getDoctrine()->getManager();
            if (count($this->getDoctrine()->getRepository(Website::class)->findAll()) == 0) {
                $website = new Website();
                $entityManager->persist($website);
                $entityManager->flush();
            }
            return $this->redirectToRoute('setup');
        }

        return $this->render('index.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
        ]);
    }

    /**
     * @Route("/setup", name="setup");
     * Cette page sert a parametrer le site lors d'une premier connexion
     */
    public function setup(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder,
                          GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $numberOfAdmins = $this->getDoctrine()->getRepository(User::class)->findAdmins();
        $numberOfAdmins = count($numberOfAdmins);
        if ($numberOfAdmins != 0) {
            return $this->redirectToRoute('homepage');
        }
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
            $user->setIsAllowedEmails(true);
            $user->setRoles(['ROLE_ADMIN']);
            #$strings = ['d','2','$','@','D',0,3,8,6,1,2,'!'];
            #$random = rand(8000,15000).$strings[rand(0,10)].rand(100,500).$strings[rand(0,10)].rand(51,9531);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new Email())
                ->from('accounts@agora.com')
                ->to($user->getEmail())
                ->subject("Confirmation de l'inscription administrateur")
                #->htmlTemplate('email/report.html.twig')
                #give a link with a random password. Link will be something like public/setPw/userid
                ->text("Bonjour " . $user->getUsername());
            $mailer->send($email);
            // do anything else you need here, like send an email

         #  return $guardHandler->authenticateUserAndHandleSuccess(
         #      $user,
         #      $request,
         #      $authenticator,
         #      'main' // firewall name in security.yaml
         #  );
            return $this->redirectToRoute('homepage');
        }


        return $this->render('setup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/configuration", name="admin_configuration");
     */
    public function configuration(Request $request)
    {

        $website = $this->getDoctrine()->getRepository(Website::class)->find(1);
        $form = $this->createForm(WebsiteType::class, $website);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "edit.success");
        }

        return $this->render('admin.config.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin", name="admin", methods={"GET"});
     */
    public function admin()
    {
        return $this->render('admin.html.twig', [
            'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
        ]);
    }

}