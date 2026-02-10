<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\CategoriesRepository;
use Admin\Repositories\ItemsRepository;
use Admin\Repositories\MediaRepository;
use Admin\Core\Database;

/**
 * ItemsController
 *
 * Doel:
 * Beheert de items-pagina's in het admin panel.
 * Haalt data op via repositories en rendert de juiste view.
 */
class ItemsController
{
    private ItemsRepository $itemsRepository;
    private ?CategoriesRepository $categoriesRepository;
    private ?MediaRepository $mediaRepository;
    private string $title = 'Items Beheer';

    /**
     * __construct()
     *
     * Doel:
     * Bewaart de repositories zodat de controller-methodes er gebruik van kunnen maken.
     * CategoriesRepository en MediaRepository zijn optioneel (niet nodig voor index).
     */
    public function __construct(
        ItemsRepository $itemsRepository,
        ?CategoriesRepository $categoriesRepository = null,
        ?MediaRepository $mediaRepository = null
    ) {
        $this->itemsRepository = $itemsRepository;
        $this->categoriesRepository = $categoriesRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * index()
     *
     * Doel:
     * Toont het overzicht van alle items in een tabel.
     *
     * Werking:
     * 1) Haalt alle items op via de repository (met categorie en media via JOINs).
     * 2) Rendert items.php via View::render().
     * 3) Geeft $title en $items door aan de view.
     */
    public function index(): void
    {
        $items = $this->itemsRepository->getAll();

        View::render('items.php', [
            'title' => $this->title,
            'items' => $items,
        ]);
    }

    /**
     * create()
     *
     * Doel:
     * Toont het formulier om een nieuw item aan te maken.
     *
     * Werking:
     * 1) Haalt alle categorieën op voor de dropdown.
     * 2) Haalt eventueel oude formulierdata op uit de Flash-sessie.
     * 3) Rendert item-create.php met categorieën en old-data.
     */
    public function create(): void
    {
        $categories = $this->categoriesRepository?->getAll() ?? [];

        // Oude invoer ophalen na een gefaalde validatie
        $old = Flash::get('old');
        if (!is_array($old)) {
            $old = [
                'name'        => '',
                'brand'       => '',
                'description' => '',
                'category_id' => '',
                'status'      => 'available',
            ];
        }

        View::render('item-create.php', [
            'title'      => 'Nieuw Item',
            'categories' => $categories,
            'old'        => $old,
        ]);
    }

    /**
     * store()
     *
     * Doel:
     * Verwerkt het formulier om een nieuw item op te slaan.
     *
     * Werking:
     * 1) Lees en saniteer POST-data.
     * 2) Valideer verplichte velden (name, category_id).
     * 3) Als er een afbeelding is geüpload:
     *    a) Controleer MIME-type en bestandsgrootte.
     *    b) Genereer een unieke bestandsnaam.
     *    c) Verplaats het bestand naar public/uploads/.
     * 4) Start een database-transactie.
     * 5) Voeg de afbeelding toe aan de media-tabel (indien aanwezig).
     * 6) Voeg het item toe aan de items-tabel met het media-ID.
     * 7) Commit de transactie.
     * 8) Bij fouten: rollback en verwijder het geüploade bestand.
     * 9) Redirect naar /admin/items met een flash-melding.
     */
    public function store(): void
    {
        // --- Stap 1: POST-data ophalen en sanitizen ---
        $name        = trim((string)($_POST['name'] ?? ''));
        $brand       = trim((string)($_POST['brand'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $categoryId  = $_POST['category_id'] ?? '';
        $status      = trim((string)($_POST['status'] ?? 'available'));

        // Oude invoer bewaren voor het geval de validatie faalt
        $oldData = [
            'name'        => $name,
            'brand'       => $brand,
            'description' => $description,
            'category_id' => $categoryId,
            'status'      => $status,
        ];
        Flash::set('old', $oldData);

        // --- Stap 2: Validatie ---
        $errors = [];

        if ($name === '') {
            $errors[] = 'Naam is verplicht.';
        }

        if ($categoryId === '' || $categoryId === null) {
            $errors[] = 'Categorie is verplicht.';
        }

        // Status moet een geldige waarde zijn
        $validStatuses = ['available', 'maintenance', 'lost', 'retired'];
        if (!in_array($status, $validStatuses, true)) {
            $errors[] = 'Ongeldige status geselecteerd.';
        }

        // --- Stap 3: Afbeelding validatie (optioneel veld) ---
        $hasImage = isset($_FILES['image'])
            && is_array($_FILES['image'])
            && (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

        $uploadedFilePath = null; // Pad naar het verplaatste bestand (voor cleanup bij fout)

        if ($hasImage) {
            $file = $_FILES['image'];

            // Maximale bestandsgrootte: 5 MB
            $maxBytes = 5 * 1024 * 1024;
            if ((int)$file['size'] > $maxBytes) {
                $errors[] = 'Afbeelding is te groot. Maximum 5 MB.';
            }

            // MIME-type controleren met finfo (veiliger dan alleen extensie)
            $tmpPath = (string)$file['tmp_name'];
            $finfo   = new \finfo(FILEINFO_MIME_TYPE);
            $mime    = (string)$finfo->file($tmpPath);

            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!array_key_exists($mime, $allowedMimes)) {
                $errors[] = 'Ongeldig bestandstype. Enkel JPG, PNG of WEBP.';
            }
        }

        // Validatie gefaald? Terug naar formulier met foutmeldingen
        if (!empty($errors)) {
            Flash::set('warning', $errors);
            header('Location: ' . ADMIN_BASE_PATH . '/items/create');
            exit;
        }

        // --- Stap 4–7: Bestand uploaden + database-transactie ---
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $mediaId = null;

            // Als er een afbeelding is, eerst uploaden en in media-tabel opslaan
            if ($hasImage) {
                $file = $_FILES['image'];
                $tmpPath = (string)$file['tmp_name'];
                $finfo   = new \finfo(FILEINFO_MIME_TYPE);
                $mime    = (string)$finfo->file($tmpPath);

                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                ];
                $ext = $allowedMimes[$mime];

                // Unieke bestandsnaam genereren (MD5 hash van random bytes)
                $filename = md5(random_bytes(16)) . '.' . $ext;

                // Uploadmap bepalen (relatief t.o.v. de projectroot)
                $projectRoot = dirname(__DIR__, 4);
                $uploadDir   = $projectRoot . '/public/uploads';

                if (!is_dir($uploadDir)) {
                    throw new \RuntimeException('Upload map ontbreekt: public/uploads');
                }

                $destination = $uploadDir . '/' . $filename;

                // Bestand verplaatsen van tijdelijke locatie naar uploads
                if (!move_uploaded_file($tmpPath, $destination)) {
                    throw new \RuntimeException('Kon bestand niet opslaan.');
                }

                // Pad onthouden voor cleanup bij rollback
                $uploadedFilePath = $destination;

                // Alt-tekst is de originele bestandsnaam zonder extensie
                $originalName = (string)$file['name'];
                $altText = pathinfo($originalName, PATHINFO_FILENAME);

                // Media-record aanmaken in de database
                $mediaId = $this->mediaRepository->createImage(
                    $originalName,
                    $filename,
                    'uploads',
                    $mime,
                    (int)$file['size'],
                    $altText
                );
            }

            // Item-record aanmaken met het (optionele) media-ID
            $this->itemsRepository->create(
                $name,
                $brand !== '' ? $brand : null,
                $description,
                $categoryId !== '' ? (int)$categoryId : null,
                $status,
                $mediaId
            );

            // Alles gelukt: transactie bevestigen
            $pdo->commit();

            // Oude formulierdata wissen
            Flash::set('old', []);
            Flash::set('success', 'Item succesvol aangemaakt.');
            header('Location: ' . ADMIN_BASE_PATH . '/items');
            exit;

        } catch (\Throwable $e) {
            // --- Stap 8: Rollback bij fouten ---
            $pdo->rollBack();

            // Geüpload bestand opruimen als het al verplaatst was
            if ($uploadedFilePath !== null && is_file($uploadedFilePath)) {
                @unlink($uploadedFilePath);
            }

            Flash::set('warning', ['Er ging iets mis: ' . $e->getMessage()]);
            header('Location: ' . ADMIN_BASE_PATH . '/items/create');
            exit;
        }
    }

    /**
     * edit()
     *
     * Doel:
     * Toont het formulier om een bestaand item te bewerken.
     *
     * Werking:
     * 1) Zoekt het item op via ID (inclusief media en categorie via JOINs).
     * 2) Haalt alle categorieën op voor de dropdown.
     * 3) Controleert of er oude formulierdata in de sessie staat (na gefaalde validatie).
     * 4) Rendert item-edit.php met het item, categorieën en eventuele old-data.
     */
    public function edit(int $id): void
    {
        $item = $this->itemsRepository->find($id);

        if ($item === null) {
            Flash::set('warning', ['Item niet gevonden.']);
            header('Location: ' . ADMIN_BASE_PATH . '/items');
            exit;
        }

        $categories = $this->categoriesRepository?->getAll() ?? [];

        // Oude invoer ophalen na een gefaalde validatie, anders item-data gebruiken
        $old = Flash::get('old');
        if (!is_array($old)) {
            $old = [
                'name'        => $item['name'],
                'brand'       => $item['brand'] ?? '',
                'description' => $item['description'],
                'category_id' => $item['category_id'] ?? '',
                'status'      => $item['status'],
            ];
        }

        View::render('item-edit.php', [
            'title'      => 'Item Bewerken',
            'item'       => $item,
            'categories' => $categories,
            'old'        => $old,
        ]);
    }

    /**
     * update()
     *
     * Doel:
     * Verwerkt het bewerkformulier en slaat wijzigingen op.
     *
     * Werking:
     * 1) Lees en saniteer POST-data.
     * 2) Valideer verplichte velden (name, category_id).
     * 3) Controleer of er een NIEUWE afbeelding is geüpload.
     *    - Ja: upload + insert in media-tabel → nieuw media-ID.
     *    - Nee: behoud het bestaande featured_media_id.
     * 4) Start een transactie, update het item, commit.
     * 5) Bij fouten: rollback en opruimen.
     * 6) Redirect met flash-melding.
     */
    public function update(int $id): void
    {
        // Controleer of het item bestaat
        $item = $this->itemsRepository->find($id);

        if ($item === null) {
            Flash::set('warning', ['Item niet gevonden.']);
            header('Location: ' . ADMIN_BASE_PATH . '/items');
            exit;
        }

        // --- Stap 1: POST-data ophalen en sanitizen ---
        $name        = trim((string)($_POST['name'] ?? ''));
        $brand       = trim((string)($_POST['brand'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $categoryId  = $_POST['category_id'] ?? '';
        $status      = trim((string)($_POST['status'] ?? 'available'));

        // Oude invoer bewaren voor het geval de validatie faalt
        $oldData = [
            'name'        => $name,
            'brand'       => $brand,
            'description' => $description,
            'category_id' => $categoryId,
            'status'      => $status,
        ];
        Flash::set('old', $oldData);

        // --- Stap 2: Validatie ---
        $errors = [];

        if ($name === '') {
            $errors[] = 'Naam is verplicht.';
        }

        if ($categoryId === '' || $categoryId === null) {
            $errors[] = 'Categorie is verplicht.';
        }

        $validStatuses = ['available', 'maintenance', 'lost', 'retired'];
        if (!in_array($status, $validStatuses, true)) {
            $errors[] = 'Ongeldige status geselecteerd.';
        }

        // --- Stap 3: Afbeelding validatie (optioneel) ---
        $hasImage = isset($_FILES['image'])
            && is_array($_FILES['image'])
            && (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

        if ($hasImage) {
            $file = $_FILES['image'];

            $maxBytes = 5 * 1024 * 1024;
            if ((int)$file['size'] > $maxBytes) {
                $errors[] = 'Afbeelding is te groot. Maximum 5 MB.';
            }

            $tmpPath = (string)$file['tmp_name'];
            $finfo   = new \finfo(FILEINFO_MIME_TYPE);
            $mime    = (string)$finfo->file($tmpPath);

            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!array_key_exists($mime, $allowedMimes)) {
                $errors[] = 'Ongeldig bestandstype. Enkel JPG, PNG of WEBP.';
            }
        }

        // Validatie gefaald? Terug naar formulier
        if (!empty($errors)) {
            Flash::set('warning', $errors);
            header('Location: ' . ADMIN_BASE_PATH . '/items/' . $id . '/edit');
            exit;
        }

        // --- Stap 4: Bestand uploaden + database-transactie ---
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        $uploadedFilePath = null;

        try {
            /**
             * Afbeelding logica:
             * - Als er een NIEUWE afbeelding is geüpload → nieuw media-record aanmaken.
             * - Als er GEEN nieuwe afbeelding is → bestaand featured_media_id behouden.
             * Dit voorkomt dat de afbeelding per ongeluk wordt gewist bij het bewerken van tekstvelden.
             */
            $mediaId = $item['featured_media_id'] !== null ? (int)$item['featured_media_id'] : null;

            if ($hasImage) {
                $file    = $_FILES['image'];
                $tmpPath = (string)$file['tmp_name'];
                $finfo   = new \finfo(FILEINFO_MIME_TYPE);
                $mime    = (string)$finfo->file($tmpPath);

                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                ];
                $ext = $allowedMimes[$mime];

                $filename = md5(random_bytes(16)) . '.' . $ext;

                $projectRoot = dirname(__DIR__, 4);
                $uploadDir   = $projectRoot . '/public/uploads';

                if (!is_dir($uploadDir)) {
                    throw new \RuntimeException('Upload map ontbreekt: public/uploads');
                }

                $destination = $uploadDir . '/' . $filename;

                if (!move_uploaded_file($tmpPath, $destination)) {
                    throw new \RuntimeException('Kon bestand niet opslaan.');
                }

                $uploadedFilePath = $destination;

                $originalName = (string)$file['name'];
                $altText = pathinfo($originalName, PATHINFO_FILENAME);

                // Nieuw media-record aanmaken → overschrijft het oude media-ID
                $mediaId = $this->mediaRepository->createImage(
                    $originalName,
                    $filename,
                    'uploads',
                    $mime,
                    (int)$file['size'],
                    $altText
                );
            }

            // Item bijwerken met (mogelijk nieuw) media-ID
            $this->itemsRepository->update(
                $id,
                $name,
                $brand !== '' ? $brand : null,
                $description,
                $categoryId !== '' ? (int)$categoryId : null,
                $status,
                $mediaId
            );

            $pdo->commit();

            Flash::set('old', []);
            Flash::set('success', 'Item succesvol bijgewerkt.');
            header('Location: ' . ADMIN_BASE_PATH . '/items');
            exit;

        } catch (\Throwable $e) {
            $pdo->rollBack();

            if ($uploadedFilePath !== null && is_file($uploadedFilePath)) {
                @unlink($uploadedFilePath);
            }

            Flash::set('warning', ['Er ging iets mis: ' . $e->getMessage()]);
            header('Location: ' . ADMIN_BASE_PATH . '/items/' . $id . '/edit');
            exit;
        }
    }

    /**
     * delete()
     *
     * Doel:
     * Verwijdert een item uit de database.
     *
     * Werking:
     * 1) Controleer of het item bestaat.
     * 2) Verwijder het item via de repository.
     * 3) Redirect naar het items-overzicht met een flash-melding.
     *
     * Let op:
     * De database-constraint ON DELETE SET NULL op featured_media_id
     * zorgt ervoor dat de media-rij intact blijft.
     */
    public function delete(int $id): void
    {
        $item = $this->itemsRepository->find($id);

        if ($item === null) {
            Flash::set('warning', ['Item niet gevonden.']);
            header('Location: ' . ADMIN_BASE_PATH . '/items');
            exit;
        }

        $this->itemsRepository->delete($id);

        Flash::set('success', 'Item verwijderd.');
        header('Location: ' . ADMIN_BASE_PATH . '/items');
        exit;
    }
}
