<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * Cette fonction retourne un json a un appel ajax. Ce JSON contient tous les signalements.
     * @Route("/report/fetch", name="report_ajax")
     */
    public function ajxFetchReports(\Symfony\Component\HttpFoundation\Request $request,ReportRepository $reportRepository): JsonResponse
    {
        #Si la requete est une requete AJAX, on renvoi en json les signalements recents
        if($request->isXmlHttpRequest())
        {
            $reports = $reportRepository->findAll();
            $jsonData = array();
            $idx = 0;
            foreach($reports as $report) {
                # On envoie les donnes de chaque forums dans un json
                $temp = array(
                   'id' => $report->getForum()->getId(),
                   'about' => $report->getUser()->getUsername() ,
                   'date' => $report->getDate(),
                   'forum-name' => $report->getForum()->getName(),
                   'forum' => $report->getForum()->getDescription(),
                );
                $jsonData[$idx++] = $temp;
            }

            return new JsonResponse($jsonData);
        }
        return new JsonResponse();
    }



    public function banFromReport()
    {

    }


}
