# Somda - CLAUDE.md

> Zie ook [.github/copilot-instructions.md](.github/copilot-instructions.md) voor uitgebreide patronen en workflows.

## Project

**Somda** is een Symfony 8.0 webapplicatie (PHP 8.4+) voor Nederlandse treinliefhebbers met forum, spotting, nieuws en rijweginformatie. Standaardlocaal: `nl`.

## Tech Stack

| Component        | Keuze                                  |
|-----------------|----------------------------------------|
| Framework        | Symfony 8.0                            |
| PHP              | 8.4+                                   |
| Database         | MySQL/MariaDB via Doctrine ORM 3.x     |
| Templates        | Twig (server-side rendering)           |
| Assets           | Statisch in `/html/` (geen bundler)    |
| Testen           | PHPUnit 9.6                            |
| Queues           | Symfony Messenger (Doctrine-backed)    |
| Sessions         | Database (PDO, tabel `somda_session`)  |

## Architectuur

```
Request → Controller → Helper → Repository → Entity → DB
                     (Business   (Queries)   (ORM)
                      Logic)
              ↓
         TemplateHelper::render() → Twig
```

- **33 Controllers** — minimale logica, delegeren naar Helpers
- **23 Helpers** (`src/Helpers/`) — alle businesslogica
- **64 Entities** (`src/Entity/`) — puur domain, geen cross-entity-kennis
- **20 Repositories** (`src/Repository/`) — query-methoden met beschrijvende namen
- **25 Forms** (`src/Form/`) — Symfony Form AbstractType subklassen
- **11 Console Commands** (`src/Command/`) — achtergrondtaken via cron

## Kritieke Conventies

### Database-naamgeving (legacy 2004, verplicht voor nieuwe tabellen)
- **3-char prefix** per tabel: 1 woord → 3 chars (`sys`), 2 woorden → 2+1 (`usp`), 3+ woorden → 1 elk (`spd`)
- **Alle kolommen** geprefixed: `usp_id`, `usp_value`
- **Foreign keys** gebruiken prefix van gerefereerde tabel: `usp_pre_id` → `pre_preference.pre_id`
- Treintijdentabellen: `somda_tdr_*` prefix

### Tijdopslag (trein-entiteiten)
Tijden opgeslagen als **minuten na 02:00 AM** (handelt dag-grenzen af):
```php
DateTrait::timeDisplayToDatabase("14:30") // → integer
DateTrait::timeDatabaseToDisplay(750)      // → "14:30"
```
Entiteiten: `TrainTable`, `TrainTableFirstLast`, `OfficialTrainTable`

### Template Rendering — altijd via TemplateHelper
```php
return $this->template_helper->render('view.html.twig', [
    TemplateHelper::PARAMETER_PAGE_TITLE => 'Titel',
    'data' => $data,
]);
```
Selecteert automatisch mobiele variant via `MobileDetect`.

### Form Handling — altijd via FormHelper
```php
$form = $this->form_helper->getFactory()->create(FormClass::class, $entity);
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    return $this->form_helper->finishFormHandling('Succes', 'route_naam', ['param' => 'waarde']);
    // finishFormHandling = flush + flash + redirect
}
```

### Routing — handmatig YAML, geen attributen
```yaml
route_naam:
  path: /pad/{parameter}/
  methods: [GET, POST]
  controller: App\Controller\SomeController::indexAction
```
Routes gebruiken Nederlandse paden (`/inloggen/`, `/spotters/`).

### Controller-methoden
Methoden eindigen op `Action` (bijv. `indexAction`, `editAction`), niet `__invoke`.

### Services
Alle services zijn `public: true` (legacy, zie `services.yaml`). Dependencies via constructor-injectie als `readonly` properties.

## Rolhiërarchie

```
ROLE_SUPER_ADMIN  ← bans, nieuws beheer, treintabellen
      ↑
  ROLE_ADMIN      ← spots, routes, gebruikersbeheer
      ↑
  ROLE_USER       ← basisgebruiker (ingelogd)
```

## Achtergrondtaken (Cron)

| Command                        | Frequentie        |
|-------------------------------|-------------------|
| `app:update-statistics`        | Elk uur           |
| `app:get-rail-news`            | 4×/uur            |
| `app:process-forum-log`        | 4×/uur            |
| `app:update-banner-statistics` | 4×/uur            |
| `app:update-locations`         | Wekelijks         |
| `app:update-route-lists`       | Dagelijks 01:30   |
| `app:update-route-trains`      | Dagelijks 01:32   |
| `app:link-trains-to-naming-pattern` | Dagelijks 01:45 |

## Ontwikkeling

```bash
ant setup              # git pull + composer install + cache clear
ant update-database    # Doctrine migrations uitvoeren
ant deploy             # Setup + chown nginx + restart php-fpm
ant phpstan            # Statische analyse
ant psalm              # Type-veilige analyse
ant phpmd              # Code kwaliteit
bin/phpunit            # Tests uitvoeren
```

## Veelvoorkomende valkuilen

- `DATABASE_URL` in `.env.local`, niet in `.env`
- Services zijn `public: true` — geen private injections nodig
- Mobiele templates in `templates/mobile/`, automatisch gekozen door `TemplateHelper`
- NS API-sleutels in `.env.local` (`NS_API_PRIMARY_KEY`, `NS_API_SECONDARY_KEY`)
- Forum is de **belangrijkste** module — voorzichtig wijzigen
- Spot-entiteit heeft een uniek constraint op train+route+location+position+user+datum

## Forum (Kern-module)

Hiërarchie: `ForumCategory → ForumForum → ForumDiscussion → ForumPost → ForumPostText`

Forum-types: `TYPE_PUBLIC` (0), `TYPE_LOGGED_IN` (1), `TYPE_MODERATORS_ONLY` (3), `TYPE_ARCHIVE` (4)

Gebruik altijd `ForumAuthorizationHelper` voor permissiechecks vóór forum-acties:
```php
if (!$this->forum_authorization_helper->mayPost($forum, $user)) { ... }
```

## Spot-systeem

- `input_feedback_flag`: bitwise flags voor validatiewaarschuwingen (nieuw treinnummer, route rijdt niet op die dag, etc.)
- `SpotHelper::getDisplaySpot()` voor weergave — verwerkt speciale routes anders dan numerieke
- Vertalingen in `translations/messages.nl.yml`
