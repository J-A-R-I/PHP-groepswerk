<?php
declare(strict_types=1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

/*
|--------------------------------------------------------------------------
| Public Front Controller
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../admin/autoload.php';

use Admin\Core\Database;
use Admin\Core\Auth;
use Admin\Repositories\ItemsRepository;
use Admin\Repositories\UsersRepository;
use Admin\Repositories\ReservationsRepository;

// 1) URL Parsing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';

// 2) Connectie
$pdo = Database::getConnection();

// 3) Repositories
$itemsRepo        = new ItemsRepository($pdo);
$usersRepo        = new UsersRepository($pdo);
$reservationsRepo = new ReservationsRepository($pdo);

// 4) Routing
switch ($uri) {

    case '/':
        // Home
        $featured = $itemsRepo->getFeatured();
        $items = $itemsRepo->getAll();
        $categories = $itemsRepo->getCategoryStats();

        $title = 'ToolTrack - Home';
        require __DIR__ . '/views/posts/home.php';
        break;

    case '/login':
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            $user = $usersRepo->findByEmail($email);

            if ($user && password_verify($password, (string)$user['password_hash'])) {
                if ((int)$user['is_active'] !== 1) {
                    $error = "Dit account is gedeactiveerd.";
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_role'] = (string)$user['role_name'];
                    $_SESSION['user_name'] = (string)$user['name'];
                    header('Location: /');
                    exit;
                }
            } else {
                $error = "E-mailadres of wachtwoord is onjuist.";
            }
        }
        require __DIR__ . '/views/auth/login.php';
        break;

    case '/register':
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string)($_POST['name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if (empty($name) || empty($email) || empty($password)) {
                $error = "Alle velden zijn verplicht.";
            } elseif ($usersRepo->findByEmail($email)) {
                $error = "Er bestaat al een account met dit e-mailadres.";
            } else {
                $usersRepo->create($email, $name, $password, 2);
                header('Location: /login');
                exit;
            }
        }
        require __DIR__ . '/views/auth/register.php';
        break;

    case '/logout':
        Auth::logout();
        header('Location: /login');
        exit;
        break;

    case '/catalogus':
        $items = $itemsRepo->getAll();
        $categories = $itemsRepo->getCategoryStats();
        $featured = null;
        $title = 'Catalogus - ToolTrack';
        require __DIR__ . '/views/posts/home.php';
        break;

    // === NIEUWE ROUTE: RESERVEREN ===
    case '/reserve':
        // 1. Auth check
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        // 2. Item ophalen
        $itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : (int)($_POST['item_id'] ?? 0);
        $item = $itemsRepo->find($itemId);

        if (!$item || $item['status'] !== 'available') {
            http_response_code(404);
            echo "Item niet gevonden of niet beschikbaar.";
            exit;
        }

        $error = null;

        // --- SLIMME DATUM LOGICA ---
        // Bepaal standaard startdatum (volgende werkdag indien na 17:00 of weekend)
        $defaultStart = date('Y-m-d');
        $currentHour  = (int)date('H');
        $dayOfWeek    = (int)date('N'); // 1 (ma) tot 7 (zo)

        // Als het na 17:00 is, of zaterdag(6)/zondag(7)
        if ($currentHour >= 17 || $dayOfWeek >= 6) {
            $d = new DateTime();
            $d->modify('+1 day'); // Eerstvolgende dag
            // Zolang het weekend is, doorschuiven
            while ($d->format('N') >= 6) {
                $d->modify('+1 day');
            }
            $defaultStart = $d->format('Y-m-d');
        }
        // ---------------------------

        // 3. Formulier verwerking (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = (string)($_POST['start_date'] ?? $defaultStart);
            $endDate   = (string)($_POST['end_date'] ?? '');
            $quantity  = (int)($_POST['quantity'] ?? 1); // Aantal ophalen
            $remarks   = trim((string)($_POST['remarks'] ?? ''));

            // Validatie
            $startTs = strtotime($startDate);
            $endTs   = strtotime($endDate);
            $nowTs   = strtotime(date('Y-m-d'));

            if (!$startTs || !$endTs) {
                $error = "Ongeldige datums ingevoerd.";
            } elseif ($startTs < $nowTs) {
                $error = "Startdatum kan niet in het verleden liggen.";
            } elseif ($endTs < $startTs) {
                $error = "Einddatum moet na de startdatum liggen.";
            } else {
                // Beschikbaarheid checken (rekening houdend met voorraad)
                $available = $reservationsRepo->getAvailableQuantity($itemId, $startDate, $endDate);

                if ($available < $quantity) {
                    $error = "Niet genoeg voorraad. Er zijn er nog maar {$available} beschikbaar in deze periode.";
                } else {
                    // Aanmaken
                    $reservationsRepo->create(
                        (int)$_SESSION['user_id'],
                        $itemId,
                        $quantity,
                        $startDate,
                        $endDate,
                        $remarks
                    );

                    header('Location: /catalogus?success=reserved');
                    exit;
                }
            }
        }

        // 4. View tonen
        $title = 'Reserveer ' . $item['name'];
        require __DIR__ . '/views/posts/reservatie-create.php';
        break;

    default:
        http_response_code(404);
        echo '404 - Pagina niet gevonden';
}