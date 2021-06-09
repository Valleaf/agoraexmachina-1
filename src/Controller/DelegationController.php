<?php

namespace App\Controller;

use App\Entity\Delegation;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Workshop;
use App\Form\DelegationThemeType;
use App\Form\DelegationWorkshopType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DelegationController Cette classe s'occuppe de l'affichage et de la gestion des délégations en BDD
 * @package App\Controller
 */
class DelegationController extends AbstractController
{

    /**
     * @Route("/user/delegation/", name="delegation_index", methods={"GET"})
     * @return Response Index de toutes les délégations reçues et envoyées
     */
    public function index(): Response
    {
        return $this->render('delegation/index.html.twig', [
            'delegationsFrom' => $this->getDoctrine()->getRepository(Delegation::class)->FindBy(
                [
                    'userFrom' => $this->getUser(),
                ]
            ),
            'delegationsTo' => $this->getDoctrine()->getRepository(Delegation::class)->FindBy(
                [
                    'userTo' => $this->getUser(),
                ]
            ),
        ]);
    }

    /**
     * @Route("/delegation/theme/{theme}/add", name="delegation_add_theme", methods={"GET", "POST"})
     * @param Request $request Requête gérant le fomulaire
     * @param Theme $theme Le thème ou la délégation sera ajoutée
     * @param TranslatorInterface $translator Permet de traduire et de prendre des variables depuis messages.xlf
     * @return Response Une page permettant d'effectuer une délégation de vote vers un autre utilisateur
     */
    public function addTheme(Request $request, Theme $theme, TranslatorInterface $translator): Response
    {
        # On vérifie que le thème autorise la délégation
        # Si non; on redirige vers l'index des ateliers du thème avec un message flash prévenant de cela.
        if ($theme->getVoteType() != 'yes-delegation')  {
            $this->addFlash("warning", $translator->trans("no.rights.delegation"));
            return $this->redirectToRoute('workshop_index', [
                'slug' => $theme->getName(),
                'theme' => $theme->getId()
            ]);
        }


        $entityManager = $this->getDoctrine()->getManager();
        #On regarde si la délégation existe déjà
        $delegation = $entityManager->getRepository(Delegation::Class)->findOneBy(
            [
                'userFrom' => $this->getUser(),
                'theme' => $theme
            ]);

        //case insert
        if (!$delegation)
            $delegation = new Delegation();

        #TODO: limiter les delegations aux utilisateurs de la categorie et ne pas pouvoir se choisir soi meme

        #On vérifie la profondeur de la délégation, si elle est nulle on la met à 1
        if ($delegation->getDeepness() == null) {
            $delegation->setDeepness(1);
        }

        $delegation->setUserFrom($this->getUser());
        $delegation->setTheme($theme);

        $form = $this->createForm(DelegationThemeType::class, $delegation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #On vérifie qu'on ne délègue pas à soi même, sinon affichage d'erreur et redirection vers la page de
            # délégations
            # TODO: Ne pqs pouvoir se choisir soi même
            if ($delegation->getUserTo() == $delegation->getUserFrom()) {
                $this->addFlash("error", "delegation.self.error");
                return $this->redirectToRoute('delegation_index');
            }

            #On vérifie que l'utilisateur à déjà des délégations. Si oui on incrémente celles qui sont en dessous de
            # la profondeur autorisée par le thème et on les délègue aussi
            $delegationsReceived = $this->getUser()->getDelegationsTo();
            if ($delegationsReceived != null) {
                foreach ($delegationsReceived as $d) {
                    $depth = $d->getDeepness();
                    if ($theme->getDelegationDeepness() == 0 || $theme->getDelegationDeepness() > $depth) {
                        $d->setUserTo($delegation->getUserTo());
                        $d->setDeepness($depth + 1);
                        $entityManager->persist($d);
                        $entityManager->flush();
                    }
                }
            }
            $entityManager->persist($delegation);
            $entityManager->flush();

            #Préparation de notification pour prévenir un utilisateur qu'il a reçu une délégation
            $notification = $delegation->getUserTo()->prepareNotification($translator->trans('delegation') . ' : '
                . $delegation->getTheme() . ' ' . $translator->trans('from') . ' '
                . $this->getUser()->getUsername());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();

            #Affichage d'un message de succès et redirection vers l'index des délégations de l'utilisateur
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('delegation_index');
        }

        #Affichage du formulaire
        return $this->render('delegation/add.theme.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/delegation/workshop/{workshop}/add", name="delegation_add_workshop", methods={"GET", "POST"})
     * @param Request $request Requête gérant le formulaire
     * @param Workshop $workshop Atelier on on ajoute la délégation
     * @return Response Fonction permettant d'ajouter une délégation à un atelier NON FONCTIONNEL
     */
    public function addWorkshop(Request $request, Workshop $workshop): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $delegation = $entityManager->getRepository(Delegation::Class)->findOneBy(
            [
                'userFrom' => $this->getUser(),
                'workshop' => $workshop
            ]);

        //case insert
        if (!$delegation)
            $delegation = new Delegation();


        $delegation->setUserFrom($this->getUser());
        $delegation->setWorkshop($workshop);

        $form = $this->createForm(DelegationWorkshopType::class, $delegation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @param Delegation $delegation Délégation à supprimer
     * @param TranslatorInterface $translator Paramètre permettant de traduire et récupérer des variables depuis
     * messages.xlf
     * @return Response Fonction qui supprime une délégation et renvoie l'utilisateur vers son index des délégations
     */
    public function delete(Delegation $delegation, TranslatorInterface $translator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($delegation);
        $entityManager->flush();

        #Envoi de notification pour l'utilisateur initialement receveur d'une délégation qu'elle vient d'être supprimée
        $notification = $delegation->getUserTo()->prepareNotification($translator->trans('delegation.deleted')
            . ' ' . $delegation->getTheme() . ' ' . $translator->trans('from')
            . ' ' . $this->getUser()->getUsername());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notification);
        $entityManager->flush();

        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('delegation_index');
    }

}