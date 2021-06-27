<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Keyword;
use App\Entity\Workshop;
use App\Entity\Theme;
use App\Form\DocumentType;
use App\Form\WorkshopDocumentsType;
use App\Form\WorkshopType;
use App\Repository\WorkshopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WorkshopController Cette classe s'occupe des ateliers, de leur gestion, affichage,  ainsi que de l'ajout des
 * documents aux ateliers et à leur affichage par mot-clé
 * @package App\Controller
 */
class WorkshopController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/admin/workshop", name="workshop_admin", methods={"GET"})
     * @param WorkshopRepository $workshopRepository Répertoire des ateliers dans la BDD
     * @return Response Fonction qui affiche pour un administrateur les ateliers
     */
    public function admin(WorkshopRepository $workshopRepository): Response
    {
        return $this->render('workshop/admin.html.twig', [
            'workshops' => $workshopRepository->findAll(),
        ]);
    }

    /**
     * On cherche si le mot cle existe deja pour l'ajouter a l'atelier si oui, sinon on retourne un keyword avec la
     * string donnée
     * @param $repo Tous les mot cles de la bdd
     * @param string $key Le mot-clé recherché
     * @return Keyword return un objet de classe Keyword, si il n'existe précédemment pas un nouveau, sinon un
     * existant depuis la BDD
     */
    public function findKeyWord($repo,string $key): Keyword
    {
        # On crée un nouveau mot-clé
        $keyword = new Keyword();
        # Trim de la key donnée, pour éviter les espaces inutiles puis association au nouveau mot clé
        $keyword->setName(strtolower(trim($key)));
        # Si aucun mot-clé n'existe dans la BDD, alors on renvoi celui-là
        if($repo == null){
            return ($keyword);
        }
        # Passage sur chaque mot de la BDD jusqu'à trouver un qui existe et qui match la key donnée. Si oui on break
        # pour sortir de la fonction et retourner ce Keyword là
        foreach ($repo as $word )
        {
            if ($word->getName() == trim($key))
            {
                $keyword=$word;
                break;
            }
        }
        # Si aucun mot n'est trouvé comme étant similaire, on retourne un nouveau mot-clé
        return $keyword;
    }

    /**
     * @Route("/admin/workshop/add", name="workshop_add")
     * @param Request $request Gère le formulaire
     * @return Response Fonction qui ajoute un atelier à la BDD
     */
    public function add(Request $request, TranslatorInterface $translator): Response
    {

        $themes = $this->getDoctrine()->getRepository(Theme::class)->findAllThemes();
        if(is_null($themes))
        {
            $this->addFlash("warning", $translator->trans('no.themes'));
            return $this->redirectToRoute('homepage');
        }

        # Création d'un nouvel atelier, attribution de l'utilisateur en tant que créateur
        $workshop = new Workshop();
        $workshop->setUser($this->security->getUser());

        # Création du formulaire et suivi avec la requête
        $form = $this->createForm(WorkshopType::class, $workshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $theme = $workshop->getTheme();


            # Ajout des mots-clés à l'atelier
            # On n'ajoute pas de mot-clé dupliqué grâce à la fonction findKeyWord()
            # Utilisation de explode pour récupérer les mots-clés séparés par des virgules, puis trim() sur chaque
            # mot-clé pour ne pas avoir d'espace inutile
            $keyRepository = $this->getDoctrine()->getRepository(Keyword::class)->findAll();
            $keywords = $workshop->getKeytext();
            if ($keywords != null) {
                $arr = explode(',', $keywords);
                foreach ($arr as $key) {
                    #trouver si le mot est dans le repositoyry, sinon en faire un nouveau Keyword
                    $keyword = $this->findKeyWord($keyRepository, $key);
                    $workshop->addKeyword($keyword);
                }
            }

            # Sauvegarde de l'atelier dans la BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workshop);
            $entityManager->flush();

            # Affichage d'un message indiquant le succès puis redirection vers la page pour modifier cet atelier
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('workshop_edit', ["workshop" => $workshop->getId()]);
        }

        return $this->render('workshop/add.html.twig', [
            'form' => $form->createView(),
            'workshop' => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/edit/{workshop}", name="workshop_edit", methods={"GET", "POST"})
     * @param Request $request Gère le formulaire
     * @param Workshop $workshop L'atelier à modifier
     * @return Response Fonction permettant de modifier un atelier
     */
    public function edit(Request $request, Workshop $workshop): Response
    {

        #On verifie que l'admin restreint est enregistré a cette catégorie, sinon on le redirige avec un message
        # flash le prévenant qu'il n'a pas les droits
        $admin = $this->getUser();
        $users = $workshop->getTheme()->getCategory()->getUsers();
        if (
        !(
            in_array('ROLE_ADMIN_RESTRICTED', $admin->getRoles())
            &&
            $users->contains($admin)
            ||
            in_array('ROLE_ADMIN', $admin->getRoles()))
        ) {
            $this->addFlash("warning", "edit.authorization");
            return $this->redirectToRoute('workshop_admin');
        }

        # Création du formulaire et suivi avec la requête
        $form = $this->createForm(WorkshopType::class, $workshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # On enlève tous les mots-clés, pour ensuite les remettre; pour éviter la duplication ou autres bugs
            # engendrés
            $keysInDb = $this->getDoctrine()->getRepository(Keyword::class)->findByWorkshopId($workshop->getId());
            if ($keysInDb != null) {
                foreach ($keysInDb as $key) {
                    $workshop->removeKeyword($key);
                }
            }
            # Ajout des mots-clés à l'atelier
            # On n'ajoute pas de mot-clé dupliqué grâce à la fonction findKeyWord()
            # Utilisation de explode pour récupérer les mots-clés séparés par des virgules, puis trim() sur chaque
            # mot-clé pour ne pas avoir d'espace inutile
            $keyRepository = $this->getDoctrine()->getRepository(Keyword::class)->findAll();
            $keywords = $workshop->getKeytext();
            if ($keywords != null) {
                $arr = explode(',', $keywords);
                foreach ($arr as $key) {
                    #trouver si le mot est dans le repositoyry, sinon en faire un nouveau Keyword
                    $keyword = $this->findKeyWord($keyRepository, $key);
                    $workshop->addKeyword($keyword);
                }
            }

            $this->getDoctrine()->getManager()->flush();
            # Affichage d'un message indiquant le succès puis redirection vers la page pour modifier cet atelier
            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
        }

        return $this->render('workshop/edit.html.twig', [
            'form' => $form->createView(),
            'workshop' => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/delete/{workshop}", name="workshop_delete")
     * @param Request $request Gère la requête
     * @param Workshop $workshop Atelier à supprimer
     * @return Response Fonction supprimant un atelier
     */
    public function delete(Request $request, Workshop $workshop): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($workshop);
        $entityManager->flush();

        # Affichage d'un message indiquant le succès puis redirection vers la page d'index des ateliers dans
        # l'administration
        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('workshop_admin');
    }

    /**
     * @Route("/{slug}/{theme}", name="workshop_index", methods={"GET"})
     * @param Request $request Requête
     * @param string $slug Partie de l'url comprenant le thème
     * @param Theme $theme Le thème choisi
     * @return Response Fonction permettant de récupérer les ateliers d'un thème depuis la BDD
     */
    public function index(Request $request, string $slug, Theme $theme): Response
    {

        if ($request->query->get('search') != "")
            $workshops = $this->getDoctrine()->getRepository(Workshop::class)->searchBy(['theme' => $theme, 'name' => $request->query->get('search')]);
        else
            $workshops = $this->getDoctrine()->getRepository(Workshop::class)->findBy(['theme' => $theme]);

        return $this->render('workshop/index.html.twig', [
            'theme' => $theme,
            'workshops' => $workshops,
        ]);
    }

    /**
     * @Route("/{slug}/workshop/{workshop}", name="workshop_show", methods={"GET"})
     * @param string $slug Partie de l'URL comprenant le thème et l'atelier
     * @param Workshop $workshop L'atelier choisi
     * @return Response Fonction qui permet de voir un atelier en détail
     */
    public function show(string $slug, Workshop $workshop): Response
    {
        return $this->render('workshop/details.html.twig', [
            'workshops' => $this->getDoctrine()->getRepository(Workshop::class)->findAllWorkshops(),
            'workshop' => $this->getDoctrine()->getRepository(Workshop::class)->findOneById($workshop),
        ]);
    }

    /**
     * @Route("/{slug}/{theme}/add", name="workshop_add_byuser", methods={"GET", "POST"})
     * @param Request $request Gère le formulaire
     * @param string $slug Partie de l'URL comprenant le thème
     * @param Theme $theme Thème où sera ajouté l'atelier
     * @return RedirectResponse|Response Fonction qui permet à un utilisateur d'ajouter un atelier à un thème
     */
    public function addByUser(Request $request, string $slug, Theme $theme)
    {
        # Création de l'atelier et attribution de l'utilisateur créateur
        $workshop = new Workshop();
        $workshop->setUser($this->security->getUser());

        # Création du formulaire et suivi avec la requête
        $form = $this->createForm(WorkshopType::class, $workshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $theme = $workshop->getTheme();


            # Ajout des mots-clés à l'atelier
            # On n'ajoute pas de mot-clé dupliqué grâce à la fonction findKeyWord()
            # Utilisation de explode pour récupérer les mots-clés séparés par des virgules, puis trim() sur chaque
            # mot-clé pour ne pas avoir d'espace inutile
            $keyRepository = $this->getDoctrine()->getRepository(Keyword::class)->findAll();
            $keywords = $workshop->getKeytext();
            if ($keywords != null) {
                $arr = explode(',', $keywords);
                foreach ($arr as $key) {
                    #trouver si le mot est dans le repositoyry, sinon en faire un nouveau Keyword
                    $keyword = $this->findKeyWord($keyRepository, $key);
                    $workshop->addKeyword($keyword);
                }
            }

            # Sauvegarde dans la BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workshop);
            $entityManager->flush();

            # Affichage d'un message indiquant le succès puis redirection vers la page du thème
            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('workshop_index', ["theme" => $theme->getId(), "slug" => $slug]);
        }

        return $this->render('workshop/add.byuser.html.twig', [
            'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAllThemes(),
            'theme' => $theme,
            'form' => $form->createView(),
            'workshop' => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/editDocument/{workshop}", name="workshop_edit_document")
     */
    public function editDocuments(Workshop $workshop): Response
    {
        return $this->render('workshop/edit-document.html.twig', [
            'workshop' => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/addDocument/{workshop}", name="workshop_add_document")
     * @param Request $request Gère le formulaire
     * @param Workshop $workshop Atelier choisi
     * @return Response Fonction qui permet d'ajouter un document à un atelier
     */
    public function addDocument(Request $request, Workshop $workshop): Response
    {
        # Si il y a deja plus de 4 documents, on redirige l'utilisateur vers la page edit avec un avertissement
        # NB : variable modifiable à souhait
        $maxDocuments = 5;
        if ($workshop->getDocuments()->count() >= $maxDocuments) {
            $this->addFlash('warning', 'Il y a deja le nombre maximum de documents');
            return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
        }

        # Si l'ajout est possible, on crée le document et le formulaire pour le suivre
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # Ajout du document à l'atelier et sauvegarde
            $workshop->addDocument($document);
            $this->getDoctrine()->getManager()->flush();

            # Message flash indiquant le succès et redirection vers l'atelier
            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute("workshop_edit", ["workshop" => $workshop->getId()]);
        }

        return $this->render('workshop/add-document.html.twig', [
            'form' => $form->createView(),
            'workshop' => $workshop,
        ]);
    }

    /**
     * @Route("/admin/workshop/deleteDocument/{workshop}/{document}", name="workshop_delete_document")
     * @param Workshop $workshop Atelier choisi
     * @param Document $document Document à supprimer
     * @return Response Fonction permettant de supprimer un document
     */
    public function deleteDocuments(Workshop $workshop, Document $document): Response
    {

        //TODO: Rajouter un renvoi si les id n'existent pas
        $workshop->removeDocument($document);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($workshop);
        $entityManager->flush();

        $this->addFlash("success", "delete.success");
        //TODO: Erreur en redirigeant vers les documents, redirige vers la page admin donc, probleme de paramètre
        return $this->redirectToRoute('admin', [
        ]);
    }

    /**
     * @Route("/workshop/keyword/{id}", name="workshop_tags")
     * @param int $id Identifiant du mot-clé
     * @param WorkshopRepository $repository Répertoire des ateliers
     * @return Response Fonction qui affiche les ateliers selon le mot-clé choisi
     */
    public function workshopsKeyword(int $id, WorkshopRepository $repository): Response
    {
        # On récupère l'utilisateur, pou avoir ses catégories dans la requête SQL
        $user = $this->getUser();
        $workshops = $repository->searchByKeyword($id,$user->getId());

        # On ne récupère donc que les ateliers correspondant au mot clé, qui sont public et dont l"utilisateur est
        # souscrit à la catégorie
        return  $this->render('workshop/index-by-keyword.html.twig',[
            'workshops'=>$workshops
        ]);
    }



}