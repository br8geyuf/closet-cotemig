<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\LojistController;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Auth\SenhaController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WardrobeController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\RecentViewController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController; // 🆕 Importado para as novas rotas de configurações

// Página inicial
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    } elseif (auth('company')->check()) {
        return redirect()->route('company.dashboard');
    }
    return app(HomeController::class)->index();
})->name('home');

Route::get('/settings/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
    ->name('notifications.index')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Usuário comum
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/esqueci-senha', [SenhaController::class, 'formEsqueci'])->name('senha.form');
    Route::post('/esqueci-senha', [SenhaController::class, 'enviarLink'])->name('senha.enviar');
    Route::get('/reset-password/{token}', [SenhaController::class, 'formReset'])->name('password.reset');
    Route::post('/reset-password', [SenhaController::class, 'salvarNovaSenha'])->name('password.update');

    // ✅ Login com Google
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🆕 Sistema de busca estilo Shopee
    Route::get('/search', [SearchController::class, 'index'])->name('search.items');

    // Perfis
    Route::get('/profile', function () {
        return app(UserController::class)->show(auth()->id());
    })->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.updateAvatar');

    // Usuários públicos + follow
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/follow', [UserController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{id}/unfollow', [UserController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/search-users', [UserController::class, 'search'])->name('users.search');

    // Itens
    Route::resource('items', ItemController::class);
    Route::get('/items/favorites/list', [ItemController::class, 'favorites'])->name('items.favorites');
    Route::patch('/items/{item}/toggle-favorite', [ItemController::class, 'toggleFavorite'])->name('items.toggle-favorite');
    Route::patch('/items/{item}/increment-usage', [ItemController::class, 'incrementUsage'])->name('items.increment-usage');

    // Categorias
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Favoritos
    Route::resource('favorites', FavoriteController::class)->except(['create', 'edit']);
    Route::post('/favorites/toggle/{item}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Orçamentos
    Route::resource('budgets', BudgetController::class);
    Route::patch('/budgets/{budget}/add-expense', [BudgetController::class, 'addExpense'])->name('budgets.add-expense');
    Route::get('/budgets/{budget}/report', [BudgetController::class, 'report'])->name('budgets.report');

    // Recordações
    Route::resource('memories', MemoryController::class);
    Route::get('/memories/item/{item}', [MemoryController::class, 'byItem'])->name('memories.by-item');
    Route::patch('/memories/{memory}/toggle-favorite', [MemoryController::class, 'toggleFavorite'])->name('memories.toggle-favorite');

    // Lojas & Promoções
    Route::resource('stores', StoreController::class);
    Route::patch('/stores/{store}/toggle-active', [StoreController::class, 'toggleActive'])->name('stores.toggle-active');
    Route::resource('promotions', PromotionController::class);
    Route::get('/promotions/store/{store}', [PromotionController::class, 'byStore'])->name('promotions.by-store');
    Route::patch('/promotions/{promotion}/toggle-active', [PromotionController::class, 'toggleActive'])->name('promotions.toggle-active');

    // Outros
    Route::get('/shopping-list', [ShoppingListController::class, 'index'])->name('shopping-list.index');
    Route::get('/filter', [FilterController::class, 'index'])->name('filter.index');
    Route::get('/wardrobe', [WardrobeController::class, 'index'])->name('wardrobe.index');
    Route::get('/api/recommendations/{item_id}', [App\Http\Controllers\RecommendationController::class, 'getRecommendations'])->name('api.recommendations');
    Route::resource('lojists', LojistController::class);

    // Checkout Pix
    Route::post('/checkout/pix', [CheckoutController::class, 'pix'])->name('checkout.pix');

    // Comunicação
    Route::prefix("communication")->name("communication.")->group(function () {
        Route::get("/", [CommunicationController::class, "index"])->name("index");
        Route::get("/{conversation}", [CommunicationController::class, "show"])->name("show");
        Route::post("/{conversation}/send", [CommunicationController::class, "sendMessage"])->name("send");
        Route::post("/create", [CommunicationController::class, "createConversation"])->name("create");
        Route::post("/{message}/read", [CommunicationController::class, "markMessageAsRead"])->name("mark-read");
        Route::delete("/{conversation}", [CommunicationController::class, "deleteConversation"])->name("delete");
        Route::get("/unread/count", [CommunicationController::class, "getUnreadCount"])->name("unread-count");
    });

    // Carrinho
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{item}', [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // Avaliações
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/items/{item}', [ReviewController::class, 'store'])->name('store');
        Route::patch('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
        Route::get('/items/{item}/list', [ReviewController::class, 'getItemReviews'])->name('item-reviews');
    });

    // Listas de Desejos
    Route::prefix('wishlists')->name('wishlists.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/', [WishlistController::class, 'store'])->name('store');
        Route::get('/{wishlist}', [WishlistController::class, 'show'])->name('show');
        Route::patch('/{wishlist}', [WishlistController::class, 'update'])->name('update');
        Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
        Route::post('/{wishlist}/items/{item}', [WishlistController::class, 'addItem'])->name('add-item');
        Route::delete('/{wishlist}/items/{item}', [WishlistController::class, 'removeItem'])->name('remove-item');
    });

    // Visualizações Recentes
    Route::prefix('recent-views')->name('recent-views.')->group(function () {
        Route::get('/', [RecentViewController::class, 'index'])->name('index');
        Route::post('/record/{item}', [RecentViewController::class, 'record'])->name('record');
        Route::get('/list', [RecentViewController::class, 'getRecent'])->name('list');
        Route::delete('/', [RecentViewController::class, 'clear'])->name('clear');
    });

    // Programa de Fidelidade
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('index');
        Route::get('/balance', [LoyaltyController::class, 'getBalance'])->name('balance');
        Route::get('/transactions', [LoyaltyController::class, 'getTransactions'])->name('transactions');
        Route::post('/redeem', [LoyaltyController::class, 'redeemDiscount'])->name('redeem');
        Route::get('/rewards', [LoyaltyController::class, 'rewards'])->name('rewards');
    });

    // ⚙️ Configurações do Usuário
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::get('/activities', [SettingsController::class, 'activities'])->name('activities');
        Route::get('/privacy', [SettingsController::class, 'privacy'])->name('privacy');
        Route::get('/blocked', [SettingsController::class, 'blocked'])->name('blocked');
        Route::get('/permissions', [SettingsController::class, 'permissions'])->name('permissions');
        Route::get('/accessibility', [SettingsController::class, 'accessibility'])->name('accessibility');
    });

    // API
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');
        Route::get('/categories/list', [CategoryController::class, 'list'])->name('categories.list');
        Route::get('/stores/list', [StoreController::class, 'list'])->name('stores.list');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    });
});

/*
|--------------------------------------------------------------------------
| Empresa (Company)
|--------------------------------------------------------------------------
*/
Route::prefix('company')->name('company.')->group(function () {
    Route::middleware('guest:company')->group(function () {
        Route::get('/login', [CompanyAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CompanyAuthController::class, 'login'])->name('login.store');
        Route::get('/register', [CompanyAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [CompanyAuthController::class, 'register'])->name('register.store');
    });

    Route::post('/logout', [CompanyAuthController::class, 'logout'])->name('logout')->middleware('auth:company');

    Route::middleware('auth:company')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'companyDashboard'])->name('dashboard');
    });
});
