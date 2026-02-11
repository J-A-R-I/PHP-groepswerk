# CHANGELOG

## Jari

### Uitgewerkte onderdelen

### Verantwoordelijke bestanden

## Nikita

### Uitgewerkte onderdelen

* **Frontend & Design Architectuur:**
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