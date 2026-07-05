<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;


class HomeController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела главной страницы.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly PostInterface $posts,
    ) {
    }

    /**
     * Назначение: обрабатывает основной маршрут раздела главной страницы.
     *
     * Действие: подготавливает данные страницы и возвращает целевое представление.
     */
    public function __invoke(): View|Factory|Application
    {

        $conditions = ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
        $postConditions = ['is_published' => ACTIVE_STATUS_ACTIVE];

        $data['title'] = __('Home');
        $data['stockPairs'] = $this->stockPairs->getAllStockPairDetailByConditions($conditions);
        $data['posts'] = $this->posts->getLatestByCondition($postConditions,3, ['comments']);

        return view('home', $data);
    }
}
