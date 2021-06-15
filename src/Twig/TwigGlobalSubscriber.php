<?php


namespace App\Twig;


use App\Entity\Website;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

/**
 * Class TwigGlobalSubscriber Est utilisé en couple avec l'entité Website pour donner accès à cette variable dans
 * toutes les templates Twig
 * @package App\Twig
 */
class TwigGlobalSubscriber implements EventSubscriberInterface
{

    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(Environment $twig, EntityManagerInterface $manager)
    {
        $this->twig = $twig;
        $this->manager = $manager;
    }

    public function injectGlobalVariables(RequestEvent $event)
    {
        $website = $this->manager->getRepository(Website::class)->find(1);
        if($website != null)
        {
            $this->twig->addGlobal('website', $website);
        }
        else
        {
            $this->twig->addGlobal('website', new Website());
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => 'injectGlobalVariables'];
    }

}