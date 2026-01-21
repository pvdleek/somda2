# Somda Codebase Guide

## Project Overview
Somda is a Symfony 7.4 web application (PHP 8.2+) for Dutch railway enthusiasts, providing train spotting, forums, news, and route information. It includes a web interface with mobile detection.

## Architecture

### MVC Pattern (Critical)
Somda follows strict MVC separation with a Helper layer:
- **Models/Entities**: No knowledge of other entities. Only pure domain logic (e.g., `TrainTableYear::isActive()`). Use Doctrine attributes for ORM mapping.
- **Controllers**: Minimal logic - only process forms, collect data, delegate to helpers. Use constructor injection for dependencies. Return `Response` via `TemplateHelper::render()`.
- **Helpers** (`src/Helpers/`): Business logic layer between controllers and entities. 22 helpers including `UserHelper`, `TemplateHelper`, `ForumHelper`, `SpotHelper`, etc. Controllers should delegate complex operations here.
- **Repositories**: Custom query methods with descriptive names (e.g., `findForDashboard()`). Often use raw SQL for complex queries.

### Database Naming Convention
Legacy 2004 design with specific rules (new tables must comply):
- **3-char prefix** from table name: 
  - 1 word: first 3 chars (`sys_system`)
  - 2 words: 2 chars + 1 char (`usp_user_preference`)
  - 3+ words: 1 char each (`spd_system_preference_domain`)
- **All columns prefixed** with table abbreviation (`usp_id`, `usp_value`)
- **Foreign keys** use referenced table's prefix (`usp_pre_id` → `pre_preference.pre_id`)
- Train table entities use `somda_tdr_*` prefix

### Time Storage Convention
Train times stored as **minutes after 2:00 AM** (handles day boundaries). Use `DateTrait` helpers:
- `timeDisplayToDatabase("14:30")` → integer
- `timeDatabaseToDisplay(750)` → "14:30"
Entities using this: `TrainTable`, `TrainTableFirstLast`, `OfficialTrainTable`

## Development Workflows

### Setup
```bash
ant setup              # Git pull, composer install, cache clear
ant update-database    # Run migrations
```

### Testing
1. Copy `phpunit.xml.dist` to `phpunit.xml`
2. Configure separate test database in `phpunit.xml`
3. Run: `bin/phpunit`
4. Generate test env: `php composer.phar dump-env test`

### Code Quality
```bash
ant phpmd      # PHP Mess Detector (config/phpmd-ruleset.xml)
ant psalm      # Static analysis
ant phpstan    # Static analysis
```

### Deployment
```bash
ant deploy     # Setup + chown nginx:nginx + restart php-fpm
```

## Key Patterns

### Dependency Injection
- All services autowired and public (`services.yaml`)
- Controllers get dependencies via constructor (readonly properties)
- Repositories extend `ServiceEntityRepository`

### Template Rendering
Always use `TemplateHelper::render()` (not Twig directly):
```php
return $this->template_helper->render('view.html.twig', [
    TemplateHelper::PARAMETER_PAGE_TITLE => 'Title',
    'data' => $data,
]);
```
Templates auto-select mobile variants via `MobileDetect`.

### User Management
Use `UserHelper` for all user operations:
- `getUser()`: Current logged-in user
- `getPreferenceByKey(UserPreference::KEY_*)`: User preferences
- `denyAccessUnlessGranted()`: Authorization
- `userIsLoggedIn()`: Auth check

### Form Handling Pattern
Forms follow a consistent pattern using `FormHelper`:
```php
$form = $this->form_helper->getFactory()->create(FormClass::class, $entity);
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    // Process form data
    return $this->form_helper->finishFormHandling(
        'Success message',
        'route_name',
        ['param' => 'value']
    );
}
```
- Forms extend `AbstractType` (namespace `App\Form\ClassName`)
- Form fields use constants (e.g., `ForumPost::FIELD_TITLE`)
- `FormHelper` provides `getDoctrine()`, `getFactory()`, `getFlashHelper()`, `getRedirectHelper()`
- `finishFormHandling()` does flush + flash message + redirect

### Validation
- Use Symfony `Assert` constraints on entity properties
- Constants for validation values (e.g., `ForumForum::TYPE_VALUES`)
- Custom validation in form types via `configureOptions()`

### Routing
Manual YAML routes (`config/routes.yaml`):
```yaml
route_name:
  path: /path/{parameter}/
  methods: [GET, POST]
  controller: App\Controller\SomeController::actionAction
```

## Forum System (Critical Component)

The forum is the most important part of Somda. It has a complex entity hierarchy and specialized helpers.

### Forum Entity Structure
- **ForumCategory** → **ForumForum** → **ForumDiscussion** → **ForumPost** → **ForumPostText**
- Forums have 4 types (constants in `ForumForum`):
  - `TYPE_PUBLIC` (0): Anyone can view
  - `TYPE_LOGGED_IN` (1): Requires login
  - `TYPE_MODERATORS_ONLY` (3): Only moderators
  - `TYPE_ARCHIVE` (4): Read-only archived content
- Each forum can have multiple moderators (many-to-many with `User`)
- Posts track edit history (`edit_timestamp`, `edit_uid`, `edit_reason`)
- Post text separated in `ForumPostText` with `new_style` flag (old BBCode vs HTML)

### Forum Helpers (5 specialized)
1. **ForumAuthorizationHelper**: Permission checking
   - `mayView(ForumForum, ?User)`: Can user see forum?
   - `mayPost(ForumForum, ?User)`: Can user post?
   - `userIsModerator(ForumForum, ?User)`: Is moderator?
2. **ForumHelper**: Text processing and display
   - `getDisplayForumPost(ForumPost)`: Handles old/new style text, quotes, links, smileys, signatures
   - Converts `%quote%`/`%unquote%` to `<blockquote>` tags
   - `replaceStaticData()`: Replaces abbreviations via `StaticDataHelper`
3. **ForumDiscussionHelper**: Discussion operations
   - `setDiscussion()`: Loads discussion with auth check
   - `getPosts()`: Paginated posts, marks as read, increments view count
   - Tracks read status per user in `somda_forum_last_read`
4. **ForumOverviewHelper**: Forum listings and overview pages
5. **ForumSearchHelper**: Search functionality with word indexing

### Forum Workflow
```php
// Check authorization first
if (!$this->forum_authorization_helper->mayPost($forum, $user)) {
    return $redirect->redirectToRoute('forum');
}

// Add new post
$this->form_helper->addPost(
    $discussion,
    $user,
    $signature_on,
    $post_text
);
```

### Forum Features
- Favorites (users can favorite discussions)
- Wiki integration (`ForumDiscussionWiki` - checkable wiki posts)
- Post alerts (moderator tools via `ForumPostAlert`)
- Full-text search with `ForumSearchWord` indexing
- Post log tracking (`ForumPostLog` - all actions logged)
- Pagination: `ForumGenerics::MAX_POSTS_PER_PAGE`

## Spot System

Spots are train sightings - the core user-generated content.

### Spot Entity Structure
- **Spot**: Links `Train` + `Route` + `Location` + `Position` + `User` + date
- Unique constraint on combination: train/route/location/position/user/date
- `input_feedback_flag`: Bitwise flags for validation warnings
  - `INPUT_FEEDBACK_TRAIN_NEW`: New train number
  - `INPUT_FEEDBACK_ROUTE_NEW`: New route number
  - `INPUT_FEEDBACK_ROUTE_NOT_ON_DAY`: Route doesn't run on that day
  - `INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION`: Route doesn't pass location
- **SpotExtra**: Optional extra info (public + private user notes)

### Spot Display
`SpotHelper::getDisplaySpot()` formats spots based on route type:
- Numeric routes: "Train X on date at location as route Y(position)"
- Special routes: `Route::SPECIAL_NO_SERVICE`, `SPECIAL_EXTRA_SERVICE`, etc.
- Translations in `translations/messages.nl.yml`

### Spot Forms
- Form field mapping uses `FormGenerics::KEY_*` constants
- Unmapped fields (e.g., train/route numbers) processed manually
- Entity types for relationships (Location, Position use choice callbacks)

## Train Table System

Complex system for official timetables (Dutch: "dienstregeling").

### Key Entities
- **TrainTableYear** (`somda_tdr_drgl`): Timetable periods (e.g., "2024-2025")
  - `isActive()`: Pure domain logic - checks if current date in range
- **TrainTable** (`somda_tdr`): Station stops for each train
  - Uses `DateTrait` for time conversion
- **TrainTableFirstLast** (`somda_tdr_s_e`): First/last stops
- **OfficialTrainTable**: NS official data
- **RouteList**: Collections of routes/trains
- **RouteTrain**: Links routes to train compositions

All use `somda_tdr_*` prefix (legacy naming).

## Scheduled Tasks
Cron jobs in `cron.txt`:
- `app:update-statistics` (hourly)
- `app:get-rail-news` (4x/hour)
- `app:process-forum-log` (4x/hour)
- `app:update-locations` (weekly)
- `app:update-route-lists`, `app:update-route-trains` (daily 01:30)

## Common Gotchas
- Controllers use `::actionAction()` naming (not `__invoke`)
- Services default to `public: true` (legacy compatibility)
- Routes use Dutch paths (`/inloggen/`, `/verkortingen/`)
- Forms reference via `Form\ClassName` namespace
- Mobile templates in `templates/mobile/`, auto-selected by `TemplateHelper`
- Database URL in `.env.local` (not `.env`)

## External Dependencies
- **NS API**: Railway data (keys in `.env.local`)
- **PDF**: `dompdf/dompdf` for exports
- **Mobile**: `mobiledetect/mobiledetectlib` for device detection
