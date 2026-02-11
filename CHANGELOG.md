# CHANGELOG

## Jari
### Uitgewerkte onderdelen
Front-end en Integratie
Database Koppeling: Er is een functionele verbinding gerealiseerd tussen de database en de publieke website. Dynamische data, zoals de catalogus van items en categorieën, wordt correct opgehaald en weergegeven.

Design Implementatie: Het visuele ontwerp van de applicatie is volledig uitgewerkt in code. De gebruikersinterface is responsief en consistent vormgegeven.

Authenticatie: Het systeem bevat een volledig werkende module voor gebruikersregistratie en het inloggen van bestaande gebruikers, wat toegang biedt tot afgeschermde functies zoals reserveren.

Back-end Systemen
Reservatiesysteem: De logica voor het reserveren van items is operationeel. Het systeem valideert invoerdata, controleert real-time de beschikbaarheid van voorraad op geselecteerde datums en verwerkt de aanvragen in de database.

Audit Logging Systeem: Er is een uitgebreid logsysteem geïntegreerd voor beheerders. Dit systeem registreert alle wijzigingen (aanmaken, bewerken, verwijderen) aan entiteiten en biedt de mogelijkheid om deze wijzigingen terug te draaien (rollback-functionaliteit) indien nodig.

### Verantwoordelijke bestanden
* `public/index.php` (De front controller met routing voor /reserve en de API /api/check-availability)

* `public/views/posts/home.php` (De catalogus waar de "Reserveer" knoppen nu werken)

* `public/views/posts/reservatie-create.php` (Het nieuwe formulier met datum-kiezer en live voorraad-check)

* `public/views/posts/reservations.php` (Overzicht van eigen reservaties voor de gebruiker)

* `public/views/auth/login.php` (Inlogscherm, vereist voor reserveren)

* `public/views/auth/register.php` (Registratiescherm)

* `public/views/partials/nav.php` (Navigatiebalk)

* `public/views/layouts/public.php` (De hoofd-layout waar de content in wordt geladen)

* `admin/index.php` (Admin routing en dependency injection voor de nieuwe controllers/repo's)

* `admin/classes/Admin/Repositories/ActivityLogsRepository.php` (NIEUW: Beheert het opslaan en terugdraaien van logs/data)

* `admin/classes/Admin/Controllers/ActivityLogsController.php` (NIEUW: Controller voor het logboek scherm)

* `admin/views/activity-logs.php` (NIEUW: De view waar admins de wijzigingen zien en kunnen 'reverten')

* `admin/classes/Admin/Repositories/ItemsRepository.php` (Bevat nu de SQL-logica om available_stock te berekenen)

* `admin/classes/Admin/Controllers/ItemsController.php` (Injecteert nu de Logger om acties op te slaan bij create/update/delete)

* `admin/views/items.php` (Item overzicht)

* `admin/views/item-edit.php` (Bewerkpagina met afbeelding upload en quantity)

* `admin/views/reservations.php` (Admin overzicht van alle reservaties)

* `admin/views/reservation-edit.php` (Admin bewerkpagina voor een specifieke reservatie)
## Nikita

### Uitgewerkte onderdelen

* **Design Architectuur:**
  * **Design System:** Keuze en implementatie van design voor backend pagina's. Verantwoordelijk voor de algemene visuele stijl van het admin-gedeelte (minimalistisch design, 'card'-based layout met soft shadows, consistente typografie).
  * **Layout Implementatie:** Opzetten van de herbruikbare basis-layout (Sidebar + Topbar + Content Area) die consistent wordt gebruikt over alle admin-pagina's.
  * **UI Componenten:** Ontwikkelen van standaard UI-elementen zoals buttons, formulier-inputs, tabellen en status-badges.

* **Admin Dashboard:**
  * Het hoofdscherm van het beheerpaneel opgezet met widget-statistieken (aantal items, gebruikers, etc.) en een status-chart integratie.

* **Items Management (CRUD):** Volledige implementatie van het beheren van items (gereedschap).
  * **Create Flow:** Formulier voor nieuwe items inclusief afbeeldings-upload logica, categorie selectie en validatie.
  * **Edit Flow:** Formulier voor bewerken waarbij bestaande afbeeldingen behouden blijven tenzij een nieuwe wordt geüpload.
  * **Delete Flow:** Verwijderfunctionaliteit met JavaScript-bevestiging.
  * **Filters:** Dynamisch filteren op Categorie en Status direct in het overzicht.

* **User Management:** Een volledig beheerpaneel voor gebruikers.
  * **Overzicht:** Tabel met automatisch gegenereerde avatars (op basis van initialen) en duidelijke rol/status badges.
  * **Edit Flow:** Mogelijkheid om naam, email en rol aan te passen. Wachtwoord-hash logica toegevoegd die alleen update als er een nieuw wachtwoord wordt ingevuld.
  * **Block/Unblock Logica:** Beveiligde actieknoppen om gebruikers te blokkeren/deblokkeren. Speciale check toegevoegd zodat een admin zichzelf niet kan blokkeren (visueel disabled + backend check).

* **UX Optimalisaties:**
  * **Navigatie:** Sidebar opgeschoond ("Categorieën" verwijderd, "Naar Website" toegevoegd). Logo-link gefixt zodat deze intern blijft binnen de admin.
  * **Feedback:** Visuele feedback toegevoegd (disabled state cursor, tooltips) om fouten te voorkomen.

### Verantwoordelijke bestanden

* `admin/index.php` (Routering & Autorisatie checks)
* `admin/includes/header.php` (HTML Head & Tailwind CDN/Build setup)
* `admin/includes/sidebar.php` (Layout & Navigatie logica)
* `admin/classes/Admin/Controllers/DashboardController.php`
* `admin/classes/Admin/Controllers/ItemsController.php` (CRUD Logica + Filters)
* `admin/classes/Admin/Controllers/UsersController.php` (User Logica + Self-block check)
* `admin/classes/Admin/Repositories/ItemsRepository.php` (Database queries + Filter SQL)
* `admin/classes/Admin/Repositories/UsersRepository.php` (User queries + Email update)
* `admin/classes/Admin/Repositories/CategoriesRepository.php`
* `admin/views/dashboard.php`
* `admin/views/items.php` (Overzicht + Filters)
* `admin/views/item-create.php`
* `admin/views/item-edit.php`
* `admin/views/users.php` (User tabel + UX logic)
* `admin/views/user-edit.php`
