<?php

namespace App\Controller;

use App\Repository\KeywordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KeywordController extends AbstractController
{
    /**
     * @Route("/keyword/{string}",
     *     defaults = { "string" =  "1"},
     *     options = { "expose" = true },
     *     name = "keywords_ajax_call",
     * )
     */
    public function ajaxFetchKeywords(Request $request, KeywordRepository $keywordRepository, string $string): Response
    {
        if ($request->isXmlHttpRequest()) {
           $keywords = $keywordRepository->findKeywordsStartingWith($string);
            $jsonData = array();
            $idx = 0;
           foreach($keywords as $keyword) {
               # On envoie les donnes de chaque mot clé dans un json
               $temp = array(
                   'key' => $keyword->getName(),
               );
               $jsonData[$idx++] = $temp;
           }

            return new JsonResponse($jsonData);
        }
        return new JsonResponse();
    }
}
