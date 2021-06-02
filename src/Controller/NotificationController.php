<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController Cette classe s'occupe de l'affichage des notifications
 * @package App\Controller
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     * @return Response Affichage une page contenant toutes les notifications de l'utilisateurs
     */
    public function index(): Response
    {
        #Récupère toutes les notifications de l'utilisateur
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findByUserId($this->getUser()
            ->getId());
        $entityManager = $this->getDoctrine()->getManager();
        #Pour toute notification non lue, on les change en lue en affichant la page
        foreach ($notifications as $notification)
        {
            if (!$notification->getIsRead())
            {
                $notification->setIsRead(true);
                $entityManager->persist($notification);
            }
        }
        $entityManager->flush();

        return $this->render('notification/index.html.twig', [
            'notifications'=>$notifications,
        ]);
    }
}
