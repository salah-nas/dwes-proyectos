# 🃏 Cartas Pokémon (Symfony 7.4)

Juego web hecho con Symfony: inicias sesión, ves **3 cartas boca abajo** (no sabes qué Pokémon son),
eliges una y la pones a combatir contra un **Pokémon aleatorio de cualquier tipo**. Todos tus combates
quedan guardados en un historial.

El modelo de datos sigue el diagrama entidad-relación que hay en [`docs/`](docs/).

## Qué necesitas

- **PHP 8.2 o superior** con estas extensiones: `pdo_sqlite`, `sqlite3`, `intl`, `mbstring`, `xml`, `ctype`, `iconv`
- **[Composer](https://getcomposer.org/)**
- **Conexión a internet** para ver las imágenes de los Pokémon (se cargan desde el repositorio público de PokeAPI)

No necesitas instalar ningún servidor de base de datos: por defecto se usa **SQLite**, que guarda todo en `var/data.db`.

Para comprobar que PHP tiene lo necesario:

```bash
php -v
php -m
```

> **Usuarios de Windows:** PHP suele traer `pdo_sqlite` desactivado. Mira la sección
> [Solución de problemas](#solución-de-problemas) antes de empezar.

## Puesta en marcha

```bash
# 1. Descargar el proyecto
git clone https://github.com/salah-nas/dwes-proyectos.git
cd dwes-proyectos

# 2. Instalar las dependencias
composer install

# 3. Crear la base de datos y las tablas (solo la primera vez)
php bin/console doctrine:schema:create

# 4. Cargar el catálogo de Pokémon y tipos
php bin/console app:cargar-pokemon

# 5. Arrancar el servidor
php -S localhost:8000 -t public
```

Abre <http://localhost:8000>, pulsa **Registrarse** para crear una cuenta y empieza a jugar.

> Si tienes la [Symfony CLI](https://symfony.com/download), puedes usar `symfony server:start` en lugar del paso 5.

Notas sobre los comandos:

- `doctrine:schema:create` se ejecuta **una sola vez**. Si lo repites verás `table ... already exists`; es inofensivo.
- `app:cargar-pokemon` se puede ejecutar las veces que quieras: añade los Pokémon que falten y actualiza los existentes.

### Empezar de cero

Si quieres borrar todos los datos (usuarios, combates...) y volver a empezar:

```bash
# Linux / macOS
rm var/data.db

# Windows (PowerShell)
Remove-Item var\data.db
```

Después repite los pasos 3 y 4.

## Cómo se juega

1. Regístrate e inicia sesión.
2. En **/juego** verás 3 cartas boca abajo. Elige una.
3. Se revela tu Pokémon. Pulsa **Combatir** y se enfrentará a un rival aleatorio.
4. Consulta el resultado y revisa tus combates anteriores en **/historial**.

## Cómo funciona el combate

`CombateService` calcula un «poder» a partir del ataque, la defensa, la velocidad y los HP de cada Pokémon,
con un factor de azar de ±15 %. Gana quien tenga más poder. Puedes cambiar la fórmula en
[`src/Service/CombateService.php`](src/Service/CombateService.php).

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

## Estructura del proyecto

```
config/                  Configuración (security, doctrine, twig, validator...)
migrations/              Migraciones de Doctrine (solo MySQL, ver más abajo)
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

## Usar MySQL / MariaDB

1. Crea un archivo `.env.local` (no se sube a GitHub) con tu conexión:

   ```dotenv
   DATABASE_URL="mysql://USUARIO:CLAVE@127.0.0.1:3306/pokemon_cartas?serverVersion=mariadb-10.11.6&charset=utf8mb4"
   ```

2. Crea la base de datos y las tablas:

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:create
   php bin/console app:cargar-pokemon
   ```

También necesitas la extensión `pdo_mysql` de PHP.

> Las migraciones de `migrations/` se generaron para MySQL/MariaDB y **no funcionan con SQLite**.
> Con SQLite usa siempre `doctrine:schema:create`.

## Solución de problemas

### `could not find driver`

PHP no tiene activada la extensión de base de datos. Es lo más habitual en Windows.

1. Averigua qué `php.ini` usa PHP:

   ```bash
   php --ini
   ```

   Fíjate en la línea **Loaded Configuration File**. Si pone `(none)`, copia `php.ini-development`
   (está en la carpeta de PHP) como `php.ini`.

2. Abre ese archivo y asegúrate de que estas líneas existen **sin `;` al principio**:

   ```ini
   extension_dir = "C:\ruta\a\php\ext"
   extension=pdo_sqlite
   extension=sqlite3
   extension=intl
   extension=mbstring
   ```

   Sustituye `C:\ruta\a\php\ext` por la carpeta `ext` de tu instalación de PHP, y comprueba que no haya
   dos líneas `extension_dir` (PHP usa la última).

3. Verifica que se han cargado y reinicia el servidor:

   ```bash
   php -m
   ```

   En la lista deben aparecer `pdo_sqlite` y `sqlite3`.

### `table ... already exists` al ejecutar `schema:create`

La base de datos ya estaba creada. Puedes ignorarlo, o empezar de cero como se explica en
[Empezar de cero](#empezar-de-cero).

### No se ven las imágenes de los Pokémon

Las imágenes se descargan de `raw.githubusercontent.com`. Comprueba tu conexión a internet.

### El navegador muestra un error 500

Mira el mensaje que aparece en pantalla (el proyecto arranca en modo desarrollo) o el registro en
`var/log/dev.log`.

## Notas

- Las estadísticas del catálogo son aproximadas.
- Los paquetes `symfony/web-profiler-bundle` y `symfony/maker-bundle` están en `require-dev`; Symfony Flex los
  configura al ejecutar `composer install`.
- El `.env` incluido solo contiene valores de desarrollo. **No uses el proyecto en producción tal cual**:
  define un `APP_SECRET` propio en `.env.local` y pon `APP_ENV=prod`.