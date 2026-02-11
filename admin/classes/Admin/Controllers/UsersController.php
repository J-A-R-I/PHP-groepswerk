<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Auth;
use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\RolesRepository;
use Admin\Repositories\UsersRepository;

class UsersController
{
    private UsersRepository $users;
    private RolesRepository $roles;

    public function __construct(UsersRepository $users, RolesRepository $roles)
    {
        $this->users = $users;
        $this->roles = $roles;
    }

    public function index(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        View::render('users.php', [
            'title' => 'Gebruikers',
            'users' => $this->users->getAll(),
        ]);
    }

    public function create(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        View::render('user-create.php', [
            'title' => 'Nieuwe gebruiker',
            'roles' => $this->roles->getAll(),
            'errors' => [],
            'old' => [
                'email' => '',
                'name' => '',
                'role_id' => '',
            ],
        ]);
    }

    public function store(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $name = trim((string)($_POST['name'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);

        $errors = [];

        if ($email === '') { $errors[] = 'Email is verplicht.'; }
        if ($name === '') { $errors[] = 'Naam is verplicht.'; }
        if ($password === '') { $errors[] = 'Wachtwoord is verplicht.'; }
        if ($roleId <= 0) { $errors[] = 'Kies een rol.'; }

        if (!empty($errors)) {
            View::render('user-create.php', [
                'title' => 'Nieuwe gebruiker',
                'roles' => $this->roles->getAll(),
                'errors' => $errors,
                'old' => [
                    'email' => $email,
                    'name' => $name,
                    'role_id' => (string)$roleId,
                ],
            ]);
            return;
        }

        $this->users->create($email, $name, $password, $roleId);

        Flash::set('Gebruiker aangemaakt.', 'success');
        header('Location: ' . ADMIN_BASE_PATH . '/users');
        exit;
    }

    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: ' . ADMIN_BASE_PATH . '/users');
            exit;
        }

        View::render('user-edit.php', [
            'title' => 'Gebruiker bewerken',
            'user' => $user,
            'roles' => $this->roles->getAll(),
            'errors' => [],
            'old' => [
                'name' => (string)$user['name'],
                'email' => (string)$user['email'],
                'role_id' => (string)$user['role_id'],
            ],
            'pw_errors' => [],
        ]);
    }

    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: ' . ADMIN_BASE_PATH . '/users');
            exit;
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $roleId = (int)($_POST['role_id'] ?? 0);
        $password = (string)($_POST['password'] ?? '');

        $errors = [];

        if ($name === '') { $errors[] = 'Naam is verplicht.'; }
        if ($email === '') { $errors[] = 'Email is verplicht.'; }
        // Simpele email validatie
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Email is ongeldig.'; }
        if ($roleId <= 0) { $errors[] = 'Kies een geldige rol.'; }

        // Als er fouten zijn, toon formulier opnieuw
        if (!empty($errors)) {
            View::render('user-edit.php', [
                'title' => 'Gebruiker bewerken',
                'user' => $user,
                'roles' => $this->roles->getAll(),
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'email' => $email,
                    'role_id' => (string)$roleId,
                ],
            ]);
            return;
        }

        // 1. Basisgegevens updaten
        $this->users->update($id, $name, $email, $roleId);

        // 2. Wachtwoord updaten (alleen als het veld is ingevuld)
        if ($password !== '') {
            $this->users->updatePassword($id, $password);
        }

        Flash::set('Gebruiker bijgewerkt.', 'success');
        header('Location: ' . ADMIN_BASE_PATH . '/users/' . $id . '/edit');
        exit;
    }

    // Noot: We hebben updatePassword() hier niet meer los nodig,
    // omdat dit nu in de algemene update() zit.
    // Ik laat hem weg of commentarieer hem uit.

    public function disable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: ' . ADMIN_BASE_PATH . '/users');
            exit;
        }

        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        $pwErrors = [];

        if ($password === '') { $pwErrors[] = 'Wachtwoord is verplicht.'; }
        if (strlen($password) < 8) { $pwErrors[] = 'Wachtwoord moet minstens 8 tekens zijn.'; }
        if ($password !== $confirm) { $pwErrors[] = 'Wachtwoorden komen niet overeen.'; }

        if (!empty($pwErrors)) {
            View::render('user-edit.php', [
                'title' => 'Gebruiker bewerken',
                'user' => $user,
                'roles' => $this->roles->getAll(),
                'errors' => [],
                'old' => [
                    'name' => (string)$user['name'],
                    'role_id' => (string)$user['role_id'],
                ],
                'pw_errors' => $pwErrors,
            ]);
            return;
        }

        $this->users->updatePassword($id, $password);

        Flash::set('Wachtwoord gereset.', 'success');
        header('Location: ' . ADMIN_BASE_PATH . '/users/' . $id . '/edit');
        exit;
    }

    public function disable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $this->users->disable($id);

        Flash::set('Gebruiker geblokkeerd.', 'success');
        header('Location: ' . ADMIN_BASE_PATH . '/users/' . $id . '/edit');
        exit;
    }

    public function enable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . ADMIN_BASE_PATH);
            exit;
        }

        $this->users->enable($id);

        Flash::set('Gebruiker geactiveerd.', 'success');
        header('Location: ' . ADMIN_BASE_PATH . '/users/' . $id . '/edit');
        exit;
    }
}