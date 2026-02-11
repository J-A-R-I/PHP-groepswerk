<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/config/app.php';
require __DIR__ . '/autoload.php';

use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\ErrorController;
use Admin\Controllers\ItemsController;
use Admin\Controllers\MediaController;
use Admin\Controllers\PostsController;
use Admin\Controllers\UsersController;
use Admin\Controllers\ReservationsController;
use Admin\Controllers\ActivityLogsController;
use Admin\Core\Auth;
use Admin\Core\Router;
use Admin\Models\StatsModel;
use Admin\Repositories\CategoriesRepository;
use Admin\Repositories\ItemsRepository;
use Admin\Repositories\MediaRepository;
use Admin\Repositories\PostsRepository;
use Admin\Repositories\RolesRepository;
use Admin\Repositories\UsersRepository;
use Admin\Repositories\ReservationsRepository;
use Admin\Repositories\ActivityLogsRepository;


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

if (str_starts_with($uri, ADMIN_BASE_PATH)) {
    $uri = substr($uri, strlen(ADMIN_BASE_PATH));
}

$uri = rtrim($uri, '/') ?: '/';


$publicRoutes = ['/']; 

if (!Auth::check() && !in_array($uri, $publicRoutes, true)) {
    header('Location: ' . ADMIN_BASE_PATH . '/');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

$router = new Router();

$errorController = new ErrorController();

$router->setNotFoundHandler(function (string $requestedUri) use ($errorController): void {
    $errorController->notFound($requestedUri);
});


$requireAdmin = function () use ($errorController): void {
    if (!Auth::isAdmin()) {
        $errorController->forbidden('Admin rechten vereist.');
        exit;
    }
};

$router->get('/', function ()  use ($requireAdmin): void {
    $requireAdmin();
    (new DashboardController(new StatsModel()))->index();
});


$router->get('/items', function () use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        CategoriesRepository::make(),
        null,
        ActivityLogsRepository::make()
    ))->index();
});

// Item aanmaken: formulier tonen (GET) en opslaan (POST)
$router->get('/items/create', function () use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        CategoriesRepository::make(),
        MediaRepository::make()
    ))->create();
});

$router->post('/items/store', function () use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        CategoriesRepository::make(),
        MediaRepository::make(),
        ActivityLogsRepository::make()
    ))->store();
});

// Item bewerken: formulier tonen (GET) en bijwerken (POST)
$router->get('/items/{id}/edit', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        CategoriesRepository::make(),
        MediaRepository::make()
    ))->edit($id);
});

$router->post('/items/{id}/update', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        CategoriesRepository::make(),
        MediaRepository::make(),
        ActivityLogsRepository::make()
    ))->update($id);
});

// Item verwijderen (POST voor veiligheid)
$router->post('/items/{id}/delete', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ItemsController(
        ItemsRepository::make(),
        null,
        null,
        ActivityLogsRepository::make()
    ))->delete($id);
});

// Placeholder route: categorieën overzicht
$router->get('/categories', function () use ($requireAdmin): void {
    $requireAdmin();
    \Admin\Core\View::render('categories.php', [
        'title' => 'Categorieën',
    ]);
});

// Activity Logs routes
$router->get('/activity-logs', function () use ($requireAdmin): void {
    $requireAdmin();
    (new ActivityLogsController(ActivityLogsRepository::make()))->index();
});

$router->post('/activity-logs/{id}/revert', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ActivityLogsController(ActivityLogsRepository::make()))->revert($id);
});

// Reserveringen routes
$router->get('/reservations', function () use ($requireAdmin): void {
    $requireAdmin();
    (new ReservationsController(ReservationsRepository::make()))->index();
});

$router->get('/reservations/{id}/edit', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ReservationsController(ReservationsRepository::make()))->edit($id);
});

$router->post('/reservations/{id}/update', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new ReservationsController(ReservationsRepository::make()))->update($id);
});

$router->post('/reservations/{id}/delete', function (int $id) use ($requireAdmin): void {
    //$requireAdmin();
    (new ReservationsController(ReservationsRepository::make()))->delete($id);
});

$router->get('/users', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->index();
});

$router->get('/users/create', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->create();
});

$router->post('/users/store', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->store();
});

$router->get('/users/{id}/edit', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->edit($id);
});

$router->post('/users/{id}/update', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->update($id);
});

$router->post('/users/{id}/reset-password', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->resetPassword($id);
});

$router->post('/users/{id}/disable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->disable($id);
});

$router->post('/users/{id}/enable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->enable($id);
});

$router->get('/posts', function () use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->index();
});

$router->get('/posts/create', function () use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->create();
});

$router->post('/posts/store', function () use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->store();
});

$router->get('/posts/{id}/edit', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->edit($id);
});

$router->post('/posts/{id}/update', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->update($id);
});

$router->get('/posts/{id}/delete', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->deleteConfirm($id);
});

$router->post('/posts/{id}/delete', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();

    PostsRepository::make()->delete($id);

    \Admin\Core\Flash::set('success', 'Post verwijderd.');
    header('Location: ' . ADMIN_BASE_PATH . '/posts');
    exit;
});

$router->get('/posts/{id}', function (int $id): void {
    (new PostsController(PostsRepository::make()))->show($id);
});

$router->get('/media', function () /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new MediaController(MediaRepository::make()))->index();
});

$router->get('/media/upload', function () /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new MediaController(MediaRepository::make()))->uploadForm();
});

$router->post('/media/store', function () /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new MediaController(MediaRepository::make()))->store();
});

$router->post('/media/{id}/delete', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new MediaController(MediaRepository::make()))->delete($id);
});

$router->dispatch($uri, $method);