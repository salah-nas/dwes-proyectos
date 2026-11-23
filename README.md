# Cartas Pokémon (Symfony 7.4)

El cliente inicia sesión, ve **3 cartas boca abajo** (no sabe qué Pokémon son), elige una y la pone a
combatir contra un **Pokémon aleatorio de cualquier tipo**. El modelo de datos sigue el diagrama
entidad-relación de `docs/`.

## Requisitos
- PHP 8.2 o superior con las extensiones `pdo_sqlite` (o `pdo_mysql`), `intl`, `mbstring`, `xml`, `ctype`, `iconv`
- [Composer](https://getcomposer.org/)

## Puesta en marcha

```bash
composer install

# Crear la base de datos (SQLite en var/data.db) y las tablas
php bin/console doctrine:schema:create

# Cargar el catálogo de Pokémon y tipos
php bin/console app:cargar-pokemon

# Arrancar el servidor
php -S localhost:8000 -t public
#  (o, si tienes Symfony CLI:  symfony server:start)
```

Abre <http://localhost:8000>, crea una cuenta en **Registrarse** y empieza a jugar.

### Usar migraciones en lugar de `schema:create`
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Usar MySQL / MariaDB
Edita `DATABASE_URL` en `.env` (hay un ejemplo comentado) y ejecuta antes
`php bin/console doctrine:database:create`.

## Estructura

```
config/                  Configuración (security, doctrine, twig, validator...)
migrations/              Migraciones de Doctrine
public/index.php         Punto de entrada
src/
  Command/               app:cargar-pokemon (datos iniciales)
  Controller/
    MainController         Portada
    LoginController        Login / logout
    RegistrationController Registro de clientes
    GestionController      Flujo del juego: 3 cartas -> elegir -> combatir -> historial
  Entity/                Usuario, Pokemon, Tipo, RondaEleccion, CartaOfrecida, Combate
  Form/                  RegistrationFormType
  Repository/            Un repositorio por entidad
  Security/              LoginAuthenticator
  Service/               CombateService (lógica del combate)
templates/               Vistas Twig
docs/                    Diagrama ER y diagrama de flujo (PDF)
```

## Rutas

| Ruta | Descripción |
|---|---|
| `/` | Portada (redirige a `/juego` si ya hay sesión) |
| `/registro`, `/login`, `/logout` | Cuenta del cliente |
| `/juego` | Muestra la ronda pendiente o crea una con 3 cartas ocultas |
| `/juego/elegir/{1-3}` (POST) | Elige una carta |
| `/juego/combatir` (POST) | Combate contra un rival aleatorio |
| `/juego/combate/{id}` | Resultado del combate |
| `/historial` | Combates anteriores |

## Cómo funciona el combate
`CombateService` calcula un "poder" a partir de ataque, defensa, velocidad y HP, con un factor de
azar de ±15 %. Gana quien tenga más poder. Puedes cambiar la fórmula en `src/Service/CombateService.php`.

## Notas
- Las estadísticas del catálogo son aproximadas. Las imágenes se cargan desde el repositorio público de PokeAPI.
- El paquete `symfony/web-profiler-bundle` y `symfony/maker-bundle` están en `require-dev`; Symfony Flex los configura al hacer `composer install`.
