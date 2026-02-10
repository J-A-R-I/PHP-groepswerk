<?php
declare(strict_types=1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Autoloader
require_once __DIR__ . '/../admin/autoload.php';

use Admin\Core\Database;
use Admin\Core\Auth;
use Admin\Repositories\ItemsRepository;
use Admin\Repositories\UsersRepository;

// URL Parsing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';

// DB & Repo
$pdo = Database::getConnection();
$itemsRepo = new ItemsRepository($pdo);
$usersRepo = new UsersRepository($pdo);

switch ($uri) {

    case '/':
        // HOME: Wel featured item, beperkt aantal grid items
        $featured = $itemsRepo->getFeatured();
        $items = $itemsRepo->getAll();
        $categories = $itemsRepo->getCategoryStats();

        $title = 'ToolTrack - Home';
        require __DIR__ . '/views/posts/home.php';
        break;

    case '/catalogus':
        // CATALOGUS: Geen featured item focus, ALLE items tonen
        $items = $itemsRepo->getAll();
        $categories = $itemsRepo->getCategoryStats();
        $featured = null; // Dit zorgt ervoor dat de view weet dat het catalogus is

        $title = 'Catalogus - ToolTrack';
        require __DIR__ . '/views/posts/home.php';
        break;

    case '/login':
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                header('Location: /admin');
            } else {
                header('Location: /');
            }
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

                    if ($user['role_name'] === 'admin') {
                        header('Location: /admin');
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

    case '/logout':
        Auth::logout();
        header('Location: /login');
        exit;
        break;

    default:
        http_response_code(404);
        echo '404 - Pagina niet gevonden';
}