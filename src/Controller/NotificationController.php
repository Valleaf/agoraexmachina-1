<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     */
    public function index(): Response
    {
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findByUserId($this->getUser()
            ->getId());
        $entityManager = $this->getDoctrine()->getManager();
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
