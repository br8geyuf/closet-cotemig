<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\BudgetRepositoryInterface;

class DashboardController extends Controller
{
    protected ItemRepositoryInterface $itemRepository;
    protected CategoryRepositoryInterface $categoryRepository;
    protected BudgetRepositoryInterface $budgetRepository;

    public function __construct(
        ItemRepositoryInterface $itemRepository,
        CategoryRepositoryInterface $categoryRepository,
        BudgetRepositoryInterface $budgetRepository
    ) {
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->budgetRepository = $budgetRepository;

        $this->middleware('auth');
    }

    /**
     * 📊 Exibe o dashboard do usuário logado
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Estatísticas via repositórios
        $stats = [
            'totalItems'      => $this->itemRepository->findByUser($userId)->count(),
            'favoriteItems'   => $this->itemRepository->findFavoritesByUser($userId)->count(),
            'totalCategories' => $this->categoryRepository->findByUser($userId)->count(),
            'activeBudgets'   => $this->budgetRepository->findActiveByUser($userId)->count(),
        ];

        // Estatísticas via relações do User
        $extraStats = [
            'followersCount'  => $user->followers()->count() ?? 0,
            'followingCount'  => $user->following()->count() ?? 0,
            'itemsCount'      => $user->items()->count() ?? 0,
            'categoriesCount' => $user->categories()->count() ?? 0,
            'favoritesCount'  => $user->favorites()->count() ?? 0,
            'memoriesCount'   => $user->memories()->count() ?? 0,
            // ✅ agora pega promoções via empresa do usuário
            'promotionsCount' => $user->company
                ? $user->company->promotions()->count()
                : 0,
        ];

        // Itens mais usados, menos usados e recentes
        $mostUsedItems  = $this->itemRepository->findMostUsed($userId, 5);
        $leastUsedItems = $this->itemRepository->findLeastUsed($userId, 5);
        $recentItems    = $this->itemRepository->findRecentByUser($userId, 5);

        // Orçamentos ativos
        $budgets = $this->budgetRepository->findActiveByUser($userId);

        return view('dashboard', array_merge($stats, $extraStats, [
            'mostUsedItems'  => $mostUsedItems,
            'leastUsedItems' => $leastUsedItems,
            'recentItems'    => $recentItems,
            'budgets'        => $budgets,
        ]));
    }
}
