# Diagrama entidad-relación

```mermaid
erDiagram
  USUARIO ||--o{ RONDA_ELECCION : inicia
  RONDA_ELECCION ||--|{ CARTA_OFRECIDA : muestra
  POKEMON ||--o{ CARTA_OFRECIDA : aparece_en
  POKEMON ||--|{ POKEMON_TIPO : tiene
  TIPO ||--o{ POKEMON_TIPO : clasifica
  USUARIO ||--o{ COMBATE : juega
  RONDA_ELECCION ||--o| COMBATE : origina
  POKEMON ||--o{ COMBATE : jugador
  POKEMON ||--o{ COMBATE : rival
  USUARIO ||--o{ CAPTURA : captura
  POKEMON ||--o{ CAPTURA : capturado_en
  COMBATE ||--o| CAPTURA : origina
  CAPTURA {
    int id_captura PK
    int id_usuario FK
    int id_pokemon FK
    int id_combate FK
    datetime fecha
  }
```

`CAPTURA` es la Pokédex del usuario: al ganar un combate, el Pokémon rival se captura
(una sola vez por usuario y Pokémon, restricción única `id_usuario + id_pokemon`).
`POKEMON` también tiene ahora `numero_pokedex` para ordenarlos.

El PDF `diagrama_pokemon_er_y_flujo.pdf` recoge el diseño original, sin `CAPTURA`.

| Entidad del diagrama | Clase PHP | Tabla |
|---|---|---|
| USUARIO | `src/Entity/Usuario.php` | `usuario` |
| POKEMON | `src/Entity/Pokemon.php` | `pokemon` |
| TIPO | `src/Entity/Tipo.php` | `tipo` |
| POKEMON_TIPO | ManyToMany en `Pokemon::$tipos` | `pokemon_tipo` |
| RONDA_ELECCION | `src/Entity/RondaEleccion.php` | `ronda_eleccion` |
| CARTA_OFRECIDA | `src/Entity/CartaOfrecida.php` | `carta_ofrecida` |
| COMBATE | `src/Entity/Combate.php` | `combate` |
| CAPTURA | `src/Entity/Captura.php` | `captura` |
