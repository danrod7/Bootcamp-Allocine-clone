<?php

namespace App\EventSubscriber;

use Twig\Environment as Twig;
use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    /**
     * On appelle le service MovieRepository
     */
    private $movieRepository;

    /**
     * Twig
     */
    private $twig;

    public function __construct(MovieRepository $movieRepository, Twig $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // notre écouteur ne s'exécute pas partout.
        // uniquement depuis nos controllers 
        // Récupérer le contrôleur
        $controller = $event->getController();

        // Avec les exceptions, le contrôleur n'est pas sous forme de tableau
        if (is_array($controller)) {
            // Récupérons le contrôleur, qui se trouve à l'index 0 du tableau
            // qui contient le contrôleur et la méthode à appeler
            $controller = $controller[0];
        };

        $controllerClassName = (get_class($controller));

        if (strpos($controllerClassName, 'App\Controller') === false) {
            // On sort du suscriber
            return;
        }

        // aller chercher un film au hasard
        // 2. On va chercher un film au hasard
        // @todo Utiliser ORDER BY RAND() LIMIT 1
        // dans une requête custom dans le Respository

        // En attendant, on va faire un shuffle() sur tous les films
        $movies = $this->movieRepository->findAll();
        // On mélange, on prend le premier
        shuffle($movies);
        $randomMovie = $movies[0];
        // dump($randomMovie);

        // 3. On le transmet à Twig
        $this->twig->addGlobal('randomMovie', $randomMovie);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}