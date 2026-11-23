<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Usuario;
use App\Repository\CapturaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PokedexController extends AbstractController
{
    #[Route('/pokedex', name: 'app_pokedex')]
    public function index(Request $request, EntityManagerInterface $em, CapturaRepository $capturas): Response
    {
        $usuario = $this->getUser();
        \assert($usuario instanceof Usuario);

        $todos = $em->getRepository(Pokemon::class)->findBy([], ['numeroPokedex' => 'ASC', 'id' => 'ASC']);
        $capturadas = $capturas->porPokemon($usuario);

        $filtro = $request->query->getString('filtro', 'todos');
        if (!\in_array($filtro, ['todos', 'capturados', 'pendientes'], true)) {
            $filtro = 'todos';
        }

        $entradas = [];
        foreach ($todos as $pokemon) {
            $captura = $capturadas[$pokemon->getId()] ?? null;

            if ('capturados' === $filtro && null === $captura) {
                continue;
            }
            if ('pendientes' === $filtro && null !== $captura) {
                continue;
            }

            $entradas[] = ['pokemon' => $pokemon, 'captura' => $captura];
        }

        $total = \count($todos);
        $numCapturados = \count($capturadas);

        return $this->render('pokedex/index.html.twig', [
            'entradas' => $entradas,
            'filtro' => $filtro,
            'total' => $total,
            'capturados' => $numCapturados,
            'porcentaje' => $total > 0 ? (int) round($numCapturados / $total * 100) : 0,
        ]);
    }
}
