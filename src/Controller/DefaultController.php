<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Website;
use App\Entity\Workshop;
use App\Form\RegistrationFormType;
use App\Form\WebsiteType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class DefaultController Cette classe permet d'afficher la page d'accueil du site ainsi que la page d'installation,
 * la page de configuration et la page d'administration
 * @package App\Controller
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage", methods={"GET"});
     * @return Response La page d'accueil du site. Si c'est un nouveau site, elle redirigera vers le setup du site,
     * avec en premier lieu l'enregistrement d'un utilisateur administrateur
     */
    public function index(): Response
    {

       ##Si aucun compte n'existe, rediriger vers une page pour creer un compte admin
       #$numberOfAdmins = $this->getDoctrine()->getRepository(User::class)->findAdmins();
       #$numberOfAdmins = count($numberOfAdmins);
       #if ($numberOfAdmins == 0) {
       #    $entityManager = $this->getDoctrine()->getManager();
       #    #Redirection vers la fonction setup
       #    return $this->redirectToRoute('setup');
       #}

        #Si le setup est déjà faite, on affiche la page d'accueil
        return $this->render('index.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAllThemes(),
            #Themes contient tous les thèmes présents dans la BDD
        ]);
    }

    /**
     * @Route("/setup", name="setup");
     * @param MailerInterface $mailer Permet d'envoyer un email
     * @param Request $request La requête permettant de traiter le formulaire
     * @param UserPasswordEncoderInterface $passwordEncoder Permet d'encoder le mot de passe
     * @param GuardAuthenticatorHandler $guardHandler Gère l'authentitification après enregistrement
     * @param LoginFormAuthenticator $authenticator Gère l'authentitification après enregistrement
     * @return Response Cette page sert a paramétrer le site lors d'une premier connexion
     */
    public function setup(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder,
                          GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {

        #Vérifier qu'il y ait 0 administrateurs dans la BDD. Si non renvoie à l'index
        $numberOfAdmins = $this->getDoctrine()->getRepository(User::class)->findAdmins();
        $numberOfAdmins = count($numberOfAdmins);
        if ($numberOfAdmins != 0) {
            return $this->redirectToRoute('homepage');
        }

        #Création d'un utilisateur et suivi du formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            #Par défaut, on donne le role admin à ce compte et on refuse les emails
            $user->setIsAllowedEmails(false);
            $user->setRoles(['ROLE_ADMIN']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            /*TODO: Implémenter un envoi de mot de passe par email.
            $email = (new Email())
                ->from('accounts@agora.com')
                ->to($user->getEmail())
                ->subject("Confirmation de l'inscription administrateur")
                #->htmlTemplate('email/report.html.twig')
                #give a link with a random password. Link will be something like public/setPw/userid
                ->text("Bonjour " . $user->getUsername());
            $mailer->send($email);
            // do anything else you need here, like send an email
            */
            #Renvoi vers l'accueil en étant en ligne
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        #Affichage du formulaire pour créer un comtpe administrateur
        return $this->render('setup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/configuration", name="admin_configuration");
     * @param Request $request Requête gérant le formulaire
     * @return Response Page permettant de configurer le site
     */
    public function configuration(Request $request)
    {

        # L'entité Website ne s'occupe que du premier Website, crée lors du setup. Il est ensuite modifiable ici.
        # Sont modifiables le nom du site, la version, l'auteur et l'email administrateur
        $website = $this->getDoctrine()->getRepository(Website::class)->find(1);
        $form = $this->createForm(WebsiteType::class, $website);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            #Affichage d'un message flash indiquant le succès de l'opération
            $this->addFlash("success", "edit.success");
        }

        #Affichage du formulaire
        return $this->render('admin.config.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin", name="admin", methods={"GET"});
     * @return Response Affichage la page d'admnistration
     */
    public function admin(): Response
    {
        return $this->render('admin.html.twig', [
            'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
            #Contient tous les utilisateurs, thèmes et ateliers
        ]);
    }

}