<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Entity\Tipo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cargar-pokemon',
    description: 'Carga en la base de datos el catálogo inicial de Pokémon y sus tipos.',
)]
class CargarPokemonCommand extends Command
{
    private const IMAGEN = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/%d.png';

    /** [nº Pokédex, nombre, tipos, hp, ataque, defensa, velocidad] (estadísticas base aproximadas) */
    private const POKEMON = [
        [1, 'Bulbasaur', ['Planta', 'Veneno'], 45, 49, 49, 45],
        [3, 'Venusaur', ['Planta', 'Veneno'], 80, 82, 83, 80],
        [4, 'Charmander', ['Fuego'], 39, 52, 43, 65],
        [6, 'Charizard', ['Fuego', 'Volador'], 78, 84, 78, 100],
        [7, 'Squirtle', ['Agua'], 44, 48, 65, 43],
        [9, 'Blastoise', ['Agua'], 79, 83, 100, 78],
        [25, 'Pikachu', ['Eléctrico'], 35, 55, 40, 90],
        [26, 'Raichu', ['Eléctrico'], 60, 90, 55, 110],
        [39, 'Jigglypuff', ['Normal', 'Hada'], 115, 45, 20, 20],
        [52, 'Meowth', ['Normal'], 40, 45, 35, 90],
        [54, 'Psyduck', ['Agua'], 50, 52, 48, 55],
        [59, 'Arcanine', ['Fuego'], 90, 110, 80, 95],
        [63, 'Abra', ['Psíquico'], 25, 20, 15, 90],
        [65, 'Alakazam', ['Psíquico'], 55, 50, 45, 120],
        [66, 'Machop', ['Lucha'], 70, 80, 50, 35],
        [68, 'Machamp', ['Lucha'], 90, 130, 80, 55],
        [74, 'Geodude', ['Roca', 'Tierra'], 40, 80, 100, 20],
        [92, 'Gastly', ['Fantasma', 'Veneno'], 30, 35, 30, 80],
        [94, 'Gengar', ['Fantasma', 'Veneno'], 60, 65, 60, 110],
        [95, 'Onix', ['Roca', 'Tierra'], 35, 45, 160, 70],
        [115, 'Kangaskhan', ['Normal'], 105, 95, 80, 90],
        [123, 'Scyther', ['Bicho', 'Volador'], 70, 110, 80, 105],
        [127, 'Pinsir', ['Bicho'], 65, 125, 100, 85],
        [129, 'Magikarp', ['Agua'], 20, 10, 55, 80],
        [130, 'Gyarados', ['Agua', 'Volador'], 95, 125, 79, 81],
        [131, 'Lapras', ['Agua', 'Hielo'], 130, 85, 80, 60],
        [133, 'Eevee', ['Normal'], 55, 55, 50, 55],
        [134, 'Vaporeon', ['Agua'], 130, 65, 60, 65],
        [135, 'Jolteon', ['Eléctrico'], 65, 65, 60, 130],
        [136, 'Flareon', ['Fuego'], 65, 130, 60, 65],
        [142, 'Aerodactyl', ['Roca', 'Volador'], 80, 105, 65, 130],
        [143, 'Snorlax', ['Normal'], 160, 110, 65, 30],
        [144, 'Articuno', ['Hielo', 'Volador'], 90, 85, 100, 85],
        [145, 'Zapdos', ['Eléctrico', 'Volador'], 90, 90, 85, 100],
        [146, 'Moltres', ['Fuego', 'Volador'], 90, 100, 90, 90],
        [148, 'Dragonair', ['Dragón'], 61, 84, 65, 70],
        [149, 'Dragonite', ['Dragón', 'Volador'], 91, 134, 95, 80],
        [150, 'Mewtwo', ['Psíquico'], 106, 110, 90, 130],
        [151, 'Mew', ['Psíquico'], 100, 100, 100, 100],
    ];

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tipoRepo = $this->em->getRepository(Tipo::class);
        $pokemonRepo = $this->em->getRepository(Pokemon::class);

        /** @var array<string, Tipo> $tipos */
        $tipos = [];
        $nuevos = 0;
        $actualizados = 0;

        foreach (self::POKEMON as [$dex, $nombre, $nombresTipos, $hp, $ataque, $defensa, $velocidad]) {
            $existente = $pokemonRepo->findOneBy(['nombre' => $nombre]);
            if (null !== $existente) {
                if (null === $existente->getNumeroPokedex()) {
                    $existente->setNumeroPokedex($dex);
                    ++$actualizados;
                }
                continue;
            }

            $pokemon = (new Pokemon())
                ->setNombre($nombre)
                ->setNumeroPokedex($dex)
                ->setImagenUrl(sprintf(self::IMAGEN, $dex))
                ->setHp($hp)
                ->setAtaque($ataque)
                ->setDefensa($defensa)
                ->setVelocidad($velocidad);

            foreach ($nombresTipos as $nombreTipo) {
                if (!isset($tipos[$nombreTipo])) {
                    $tipo = $tipoRepo->findOneBy(['nombre' => $nombreTipo]);
                    if (null === $tipo) {
                        $tipo = (new Tipo())->setNombre($nombreTipo);
                        $this->em->persist($tipo);
                    }
                    $tipos[$nombreTipo] = $tipo;
                }
                $pokemon->addTipo($tipos[$nombreTipo]);
            }

            $this->em->persist($pokemon);
            ++$nuevos;
        }

        $this->em->flush();

        $io->success(sprintf('%d Pokémon nuevos cargados y %d actualizados con su número de Pokédex.', $nuevos, $actualizados));

        return Command::SUCCESS;
    }
}
