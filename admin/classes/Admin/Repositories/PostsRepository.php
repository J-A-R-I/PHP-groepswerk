<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

final class PostsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function make(): self
    {
        return new self(Database::getConnection());
    }

    // MOCK DATA: Een array met fake posts
    private function getMockPosts(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Welkom bij mijn Project',
                'content' => 'Dit is een automatisch gegenereerde post om te laten zien dat het systeem werkt zonder database connectie.',
                'status' => 'published',
                'featured_media_id' => null,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'title' => 'PHP is geweldig',
                'content' => 'Met MVC en OOP kun je robuuste applicaties bouwen. Dit project demonstreert routing, controllers en views.',
                'status' => 'published',
                'featured_media_id' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'id' => 3,
                'title' => 'Verborgen concept',
                'content' => 'Deze post is een draft en zou niet op de homepage mogen staan, maar wel in de admin.',
                'status' => 'draft',
                'featured_media_id' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ]
        ];
    }

    public function getAll(): array
    {
        // Admin ziet alles
        return $this->getMockPosts();
    }

    public function getPublishedLatest(int $limit = 6): array
    {
        // Frontend ziet alleen published
        $posts = array_filter($this->getMockPosts(), fn($p) => $p['status'] === 'published');
        return array_slice($posts, 0, $limit);
    }

    public function getPublishedAll(): array
    {
        return array_filter($this->getMockPosts(), fn($p) => $p['status'] === 'published');
    }

    public function find(int $id): ?array
    {
        $posts = $this->getMockPosts();
        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                return $post;
            }
        }
        return null;
    }

    public function findPublishedById(int $id): ?array
    {
        $post = $this->find($id);
        if ($post && $post['status'] === 'published') {
            return $post;
        }
        return null;
    }

    // Dummy methodes (opslaan werkt niet echt in mock modus)
    public function create(string $title, string $content, string $status, ?int $featuredMediaId = null): int { return 99; }
    public function update(int $id, string $title, string $content, string $status, ?int $featuredMediaId = null): void {}
    public function delete(int $id): void {}
}