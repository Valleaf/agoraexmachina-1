<?php
namespace App\Controller;

use App\Entity\Proposal;
use App\Entity\Workshop;
use App\Entity\Theme;
use App\Form\ProposalType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProposalController Cette classe s'occupe de l'ajout , modification , suppression et affichage des propositiohns
 * @package App\Controller
 */
class ProposalController extends AbstractController
{

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/add", name="proposal_add", methods={"GET", "POST"})
     * @param Request $request S'occupe du formulaire
     * @param string $slug url de la page, combinaison du thème et de l'atelier
     * @param Workshop $workshop Atelier concerné
     * @return Response Page permettant d'ajouter une proposition via un formulaire
     */
	public function add(Request $request, string $slug, Workshop $workshop): Response
	{
	    #On crée une nouvelle proposition a laquelle on associe l'utilisateur
		$proposal	 = new Proposal();
		$proposal->setUser($this->getUser());
		$proposal->setWorkshop($workshop);
		$form		 = $this->createForm(ProposalType::class, $proposal);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($proposal);
			$entityManager->flush();

            #Affichage d'un message flash indiquant le succès de l'opération et on redirige vers la proposition en
            # question en donnant en paramètre le slug et l'id de l'atelier
			$this->addFlash("success", "add.success");
			return $this->redirectToRoute('workshop_show', ['slug' => $slug, 'workshop' => $workshop->getId()]);
		}

		#Affiche la page en donnant à twig tous les themes, tous les ateliers, ainsi que l'atelier qu'on souhaite
        # utiliser pour ajouter une proposition et le formulaire correspondant
		return $this->render('proposal/add.html.twig', [
					'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
					'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
					'workshop'	 => $workshop,
					'form'		 => $form->createView(),
		]);
	}

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}", name="proposal_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param string $slug url de la page, combinaison du thème et de l'atelier
     * @param Workshop $workshop Atelier concerné
     * @param Proposal|null $proposal Proposition en question qui sera mise en avant. Si aucun selectionnée la
     * première id dans la BDD sera choisie
     * @return Response Affichage une page contenant la liste des propositions de l'atelier, la liste des thèmes,
     */
	public function index(string $slug, Workshop $workshop, Proposal $proposal = null): Response
	{


        return $this->render('proposal/index.html.twig', [
					'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
					'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
					'workshop'	 => $workshop,
					'proposals'	 => $this->getDoctrine()->getRepository(Proposal::class)->findBy(['workshop' => $workshop]),
					'proposal'	 => $proposal
		]);
	}

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/{proposal}/edit", name="proposal_edit", methods={"GET", "POST"})
     * @param Request $request Requête gérant le formulaire
     * @param string $slug url de la page, combinaison du thème et de l'atelier
     * @param Proposal $proposal Proposition concernée
     * @param Workshop $workshop Atelier concerné
     * @return Response Page permettant de modifier une proposition
     */
	public function edit(Request $request, string $slug, Proposal $proposal, Workshop $workshop): Response
	{

	    $proposal->setUser($this->getUser());

		$proposal->setWorkshop($workshop);

		$form = $this->createForm(ProposalType::class, $proposal);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$this->getDoctrine()->getManager()->flush();
            # Affichage d'un message flash indiquant le succès et redirection vers la proposition après envoi de
            # formulaire
			$this->addFlash("success", "edit.success");
			return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'proposal' => $proposal->getId(), 'workshop' => $workshop->getId()]);
		}
        # Affiche le formulaire
		return $this->render('proposal/edit.html.twig', [
					'themes' => $this->getDoctrine()->getRepository(Theme::class)->findAll(),
					'workshops'	 => $this->getDoctrine()->getRepository(Workshop::class)->findAll(),
					'workshop'	 => $workshop,
					'form'		 => $form->createView(),
		]);
	}

    /**
     * @Route("/{slug}/workshop/{workshop}/proposal/delete/{proposal}", name="proposal_delete", methods={"GET"})
     * @param string $slug url de la page, combinaison du thème et de l'atelier
     * @param Proposal $proposal Proposition concernée
     * @param Workshop $workshop Atelier concerné
     * @return Response Fonction permettant de supprimer une proposition
     */
	public function delete(string $slug, Workshop $workshop, Proposal $proposal): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($proposal);
		$entityManager->flush();

		# Affiche un message flash indiquant le succès de la supression et redirection vers l'index des propositions
        # de cet atelier
		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('proposal_index', ['slug' => $slug, 'workshop' => $workshop->getId()]);
	}

    /**
     * @Route ("/proposal/request/{id}", defaults={"id"=1},name="fetch_proposal_by_id")
     * @param Request $request
     * @param int $id
     * @return Response
     */
	public function fetchProposal(Request $request,int $id): Response
    {

        $proposal = $this->getDoctrine()->getRepository(Proposal::class)->find($id);
        # Si on reçoit une requête AJAX, on retourne la proposition
        if($request->isXmlHttpRequest())
        {
            $user = $this->getUser();
            $jsonData = array();
            $idx = 0;
            $jsonData = [
                'name'=>$proposal->getName(),
                'description' => $proposal->getDescription(),
                'id'=>$proposal->getId(),
                'author'=>$proposal->getUser()->getUsername()
            ];
            $forums = $proposal->getForums();
            $jsonData['forums'] = [];
            foreach($forums as $forum) {
                # On envoie les donnes de chaque forums dans un json
                $temp = array(
                    'author' => $forum->getUser()->getUsername() ,
                    'name' => $forum->getName(),
                    'description' => $forum->getDescription(),
                );
                $jsonData['forums'][$idx++] = $temp;
            }
            $template =  $this->render('proposal/_show_.html.twig')->getContent();
            $json = json_encode([$template,$jsonData]);
            $response = new Response($json,200);
            $response->headers->set('Content-type','application/json');
            return  $response;
            #return new JsonResponse($jsonData);
        }
        return new Response();
    }


    public function fetchProposal2(int $id)
    {
        $proposal = $this->getDoctrine()->getRepository(Proposal::class)->find($id);
        return $this->render('proposal/_proposal_show_.twig',[
            'proposal'=>$proposal
        ]);
    }

}