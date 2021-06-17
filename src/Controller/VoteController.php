<?php
namespace App\Controller;

use App\Entity\Vote;
use App\Entity\Proposal;
use App\Entity\User;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VoteController Cette classe s'occupe de l'ajout et la suppression des votes
 * @package App\Controller
 */
class VoteController extends AbstractController
{

    /**
     * @Route("/{slug}/workshop/proposal/{proposal}/vote/{userVote}/user/{user}", name="vote_add", methods={"GET", "POST"})
     * @param Request $request Gère le formulaire
     * @param string $slug partie de l'URL de la page, composé du thème et de l'atelier
     * @param Proposal $proposal La proposition sur laquelle le vote se fait
     * @param string $userVote La string envoyé par notre template selon le clic de l'utilisateur sur pour, neutre ou
     * contre. Utilisé pour connaitre la valeur du vote
     * @param User $user L'utilisateur votant
     * @return Response Cette fonction ajoute un vote à une proposition selon les paramètres donnés
     */
	public function add(Request $request, string $slug, Proposal $proposal, string $userVote, User $user): Response
	{
	    #TODO: VOTER A POIDS : MODAL QUI DEMANDE LE NOMBRE DE POINTS ?
		$entityManager = $this->getDoctrine()->getManager();
        # On regarde si le vote existe déjà, ou non
		$vote = $entityManager->getRepository(Vote::class)->findOneBy([
			'user'		 => $user,
			'proposal'	 => $proposal
				]
		);

		//case insert
		if( ! $vote)
			$vote = new Vote();

		$vote->setUser($this->getUser());
		$vote->setProposal($proposal);
		$vote->setVotedFor(($userVote == "votedFor") ? 1 : 0);
		$vote->setVotedAgainst(($userVote == "votedAgainst") ? 1 : 0);
		$vote->setVotedBlank(($userVote == "votedBlank") ? 1 : 0);
		$vote->setUser($user);
		
		$entityManager->persist($vote);
		$entityManager->flush();

		# On indique le succès de l'opération et on redirige l'utilisateur vers l'index des propositions et sur la
        # proposition votée mise en valeur
		$this->addFlash("success", "vote.success");
        return $this->forward('App\\Controller\\WorkshopController::show',[
            'slug'=>$slug,
            'workshop'=>$proposal->getWorkshop(),
        ]);
        #	return $this->redirectToRoute('proposal_index', [
#				'slug'		 => $slug,
#				'workshop'	 => $proposal->getWorkshop()->getId(),
#                'proposal'   => $proposal->getId()
#	]);
	}

    /**
     * @Route("/{slug}/workshop/proposal/{proposal}/vote/delete/{vote}", name="vote_delete", methods={"GET"})
     * @param Request $request Gère le formulaire
     * @param Vote $vote Vote à supprimer
     * @param string $slug Partie de l'URL de la page, composé du thème et de l'atelier
     * @return Response Fonction qui supprime un vote
     */
	public function delete(Request $request, Vote $vote, string $slug): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($vote);
		$entityManager->flush();

		
		$this->addFlash("success", "delete.success");
		return $this->redirectToRoute('proposal_index', [
					'slug'		 => $slug,
					'workshop'	 => $vote->getProposal()->getWorkshop()
		]);
	}

}