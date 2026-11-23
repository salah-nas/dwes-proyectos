<?php

namespace App\Controller;

use App\Entity\CartaOfrecida;
use App\Entity\Captura;
use App\Entity\Combate;
use App\Entity\RondaEleccion;
use App\Entity\Usuario;
use App\Repository\CapturaRepository;
use App\Repository\CombateRepository;
use App\Repository\PokemonRepository;
use App\Repository\RondaEleccionRepository;
use App\Service\CombateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestiona el flujo del juego:
 * ver 3 cartas ocultas -> elegir una -> combatir contra un rival aleatorio.
 */
#[IsGranted('ROLE_USER')]
class GestionController extends AbstractController
{
    private function usuario(): Usuario
    {
        $usuario = $this->getUser();
        \assert($usuario instanceof Usuario);

        return $usuario;
    }

    /** Muestra la ronda pendiente del usuario; si no hay ninguna, crea una con 3 cartas nuevas. */
    #[Route('/juego', name: 'app_juego')]
    public function juego(
        RondaEleccionRepository $rondas,
        PokemonRepository $pokemons,
        EntityManagerInterface $em,
    ): Response {
        $usuario = $this->usuario();
        $ronda = $rondas->findPendiente($usuario);

        if (null === $ronda) {
            $elegidos = $pokemons->aleatorios(3);

            if (3 === count($elegidos)) {
                $ronda = (new RondaEleccion())->setUsuario($usuario);
                $em->persist($ronda);
                $em->flush(); // La ronda necesita su id antes de crear las cartas (clave compuesta).

                foreach ($elegidos as $i => $pokemon) {
                    $carta = (new CartaOfrecida())
                        ->setRonda($ronda)
                        ->setPokemon($pokemon)
                        ->setPosicion($i + 1);
                    $ronda->addCarta($carta);
                    $em->persist($carta);
                }
                $em->flush();
            }
        }

        return $this->render('gestion/juego.html.twig', ['ronda' => $ronda]);
    }

    #[Route('/juego/elegir/{posicion}', name: 'app_juego_elegir', requirements: ['posicion' => '[1-3]'], methods: ['POST'])]
    public function elegir(
        int $posicion,
        Request $request,
        RondaEleccionRepository $rondas,
        EntityManagerInterface $em,
    ): Response {
        if (!$this->isCsrfTokenValid('elegir'.$posicion, $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Token de seguridad no válido. Inténtalo de nuevo.');

            return $this->redirectToRoute('app_juego');
        }

        $ronda = $rondas->findPendiente($this->usuario());
        $carta = $ronda?->getCartaEnPosicion($posicion);

        if (null !== $ronda && null === $ronda->getPokemonElegido() && null !== $carta) {
            $carta->setElegida(true);
            $ronda->setPokemonElegido($carta->getPokemon());
            $em->flush();
        }

        return $this->redirectToRoute('app_juego');
    }

    #[Route('/juego/combatir', name: 'app_juego_combatir', methods: ['POST'])]
    public function combatir(
        Request $request,
        RondaEleccionRepository $rondas,
        PokemonRepository $pokemons,
        CapturaRepository $capturas,
        CombateService $combateService,
        EntityManagerInterface $em,
    ): Response {
        if (!$this->isCsrfTokenValid('combatir', $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Token de seguridad no válido. Inténtalo de nuevo.');

            return $this->redirectToRoute('app_juego');
        }

        $usuario = $this->usuario();
        $ronda = $rondas->findPendiente($usuario);
        $elegido = $ronda?->getPokemonElegido();

        if (null === $ronda || null === $elegido) {
            return $this->redirectToRoute('app_juego');
        }

        // Rival aleatorio de todo el catálogo, sin filtrar por tipo.
        $rival = $pokemons->unoAleatorio();
        if (null === $rival) {
            return $this->redirectToRoute('app_juego');
        }

        $combate = (new Combate())
            ->setUsuario($usuario)
            ->setRonda($ronda)
            ->setPokemonJugador($elegido)
            ->setPokemonRival($rival)
            ->setResultado($combateService->resolver($elegido, $rival));

        $em->persist($combate);

        // Si ganas, capturas al rival (solo una vez por Pokémon) y entra en tu Pokédex.
        if (Combate::VICTORIA === $combate->getResultado() && !$capturas->existe($usuario, $rival)) {
            $em->persist(
                (new Captura())
                    ->setUsuario($usuario)
                    ->setPokemon($rival)
                    ->setCombate($combate)
            );
        }

        $em->flush();

        return $this->redirectToRoute('app_combate', ['id' => $combate->getId()]);
    }

    #[Route('/juego/combate/{id}', name: 'app_combate', requirements: ['id' => '\d+'])]
    public function combate(int $id, CombateRepository $combates, CapturaRepository $capturas): Response
    {
        $combate = $combates->find($id);

        if (null === $combate || $combate->getUsuario()?->getId() !== $this->usuario()->getId()) {
            throw $this->createNotFoundException('Combate no encontrado.');
        }

        return $this->render('gestion/combate.html.twig', [
            'combate' => $combate,
            'captura' => $capturas->findOneBy(['combate' => $combate]),
        ]);
    }

    #[Route('/historial', name: 'app_historial')]
    public function historial(CombateRepository $combates): Response
    {
        $historial = $combates->historialDe($this->usuario());

        $resumen = [Combate::VICTORIA => 0, Combate::DERROTA => 0, Combate::EMPATE => 0];
        foreach ($historial as $combate) {
            ++$resumen[$combate->getResultado()];
        }

        return $this->render('gestion/historial.html.twig', [
            'historial' => $historial,
            'resumen' => $resumen,
        ]);
    }
}
