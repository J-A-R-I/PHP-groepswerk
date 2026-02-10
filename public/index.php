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

// 1) URL Parsing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';

// 2) Connectie
$pdo = Database::getConnection();

// 3) Repositories
$itemsRepo = new ItemsRepository($pdo);
$usersRepo = new UsersRepository($pdo);

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
            if (Auth::isAdmin()) {
                header('Location: /');
            } else {
                header('Location: /');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            $user = $usersRepo->findByEmail($email);

            // Wachtwoord verificatie (werkt met password_hash uit database)
            if ($user && password_verify($password, (string)$user['password_hash'])) {
                if ((int)$user['is_active'] !== 1) {
                    $error = "Dit account is gedeactiveerd.";
                } else {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_role'] = (string)$user['role_name'];
                    $_SESSION['user_name'] = (string)$user['name'];

                    if ($user['role_name'] === 'admin') {
                        header('Location: /');
                    } else {
                        header('Location: /');
                    }
                    exit;
                }
            } else {
                $error = "E-mailadres of wachtwoord is onjuist.";
            }
        }

        require __DIR__ . '/views/auth/login.php';
        break;

    case '/register':
        // Registratie Logica
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string)($_POST['name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            // Validatie
            if (empty($name) || empty($email) || empty($password)) {
                $error = "Alle velden zijn verplicht.";
            } elseif ($usersRepo->findByEmail($email)) {
                $error = "Er bestaat al een account met dit e-mailadres.";
            } else {
                // Maak gebruiker aan.
                // We geven rol ID 2 mee (Standaard User/Student).
                // De repository hashed het wachtwoord automatisch.
                $usersRepo->create($email, $name, $password, 2);

                // Optioneel: Direct inloggen of doorsturen naar login
                header('Location: /login'); // Stuur naar login met succesbericht (kan via flash session)
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

    default:
        http_response_code(404);
        echo '404 - Pagina niet gevonden';
}