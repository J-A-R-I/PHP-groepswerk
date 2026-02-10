<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\View;
use Admin\Repositories\ItemsRepository;

/**
 * ItemsController
 *
 * Doel:
 * Beheert de items-pagina's in het admin panel.
 * Haalt data op via ItemsRepository en rendert de juiste view.
 */
class ItemsController
{
    private ItemsRepository $itemsRepository;
    private string $title = 'Items Beheer';

    /**
     * __construct()
     *
     * Doel:
     * Bewaart de repository zodat de controller-methodes er gebruik van kunnen maken.
     */
    public function __construct(ItemsRepository $itemsRepository)
    {
        $this->itemsRepository = $itemsRepository;
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
}
