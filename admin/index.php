<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/config/app.php';
require __DIR__ . '/autoload.php';

use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\ErrorController;
use Admin\Controllers\MediaController;
use Admin\Controllers\PostsController;
use Admin\Controllers\UsersController;
use Admin\Core\Auth;
use Admin\Core\Router;
use Admin\Models\StatsModel;
use Admin\Repositories\MediaRepository;
use Admin\Repositories\PostsRepository;
use Admin\Repositories\RolesRepository;
use Admin\Repositories\UsersRepository;


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

if (str_starts_with($uri, ADMIN_BASE_PATH)) {
    $uri = substr($uri, strlen(ADMIN_BASE_PATH));
}

$uri = rtrim($uri, '/') ?: '/';


$publicRoutes = ['/']; // /login

//if (!Auth::check() && !in_array($uri, $publicRoutes, true)) {
//    header('Location: ' . ADMIN_BASE_PATH . '/login');
//    exit;
//}

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

$router->get('/', function (): void {
    (new DashboardController(new StatsModel()))->index();
});

//$router->get('/login', function (): void {
//    (new AuthController(UsersRepository::make()))->showLogin();
//});
//
//$router->post('/login', function (): void {
//    (new AuthController(UsersRepository::make()))->login();
//});
//
//$router->post('/logout', function (): void {
//    (new AuthController(UsersRepository::make()))->logout();
//});

$router->get('/users', function () /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->index();
});

$router->get('/users/create', function () /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->create();
});

$router->post('/users/store', function ()/* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->store();
});

$router->get('/users/{id}/edit', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->edit($id);
});

$router->post('/users/{id}/update', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->update($id);
});

$router->post('/users/{id}/reset-password', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->resetPassword($id);
});

$router->post('/users/{id}/disable', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->disable($id);
});

$router->post('/users/{id}/enable', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->enable($id);
});

$router->get('/posts', function (): void {
    (new PostsController(PostsRepository::make()))->index();
});

$router->get('/posts/create', function (): void {
    (new PostsController(PostsRepository::make()))->create();
});

$router->post('/posts/store', function (): void {
    (new PostsController(PostsRepository::make()))->store();
});

$router->get('/posts/{id}/edit', function (int $id): void {
    (new PostsController(PostsRepository::make()))->edit($id);
});

$router->post('/posts/{id}/update', function (int $id): void {
    (new PostsController(PostsRepository::make()))->update($id);
});

$router->get('/posts/{id}/delete', function (int $id) /* use ($requireAdmin)*/: void {
    //$requireAdmin();
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