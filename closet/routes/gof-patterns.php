<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnhancedItemController;

/*
|--------------------------------------------------------------------------
| Rotas para demonstração dos padrões GoF
|--------------------------------------------------------------------------
|
| Estas rotas demonstram o uso dos padrões GoF implementados:
| - Singleton: DatabaseConnection
| - Factory Method: ItemFactory
| - Observer: EventSubject
| - Strategy: FilterContext
| - Decorator: ItemDecoratorManager
|
*/

Route::middleware(['auth'])->group(function () {
    
    // Rotas principais do sistema aprimorado
    Route::prefix('enhanced-items')->name('enhanced-items.')->group(function () {
        
        // CRUD básico com padrões GoF
        Route::get('/', [EnhancedItemController::class, 'index'])->name('index');
        Route::post('/', [EnhancedItemController::class, 'store'])->name('store');
        Route::get('/{item}', [EnhancedItemController::class, 'show'])->name('show');
        
        // Funcionalidades específicas
        Route::post('/{item}/favorite', [EnhancedItemController::class, 'addToFavorites'])->name('favorite');
        Route::post('/{item}/usage', [EnhancedItemController::class, 'recordUsage'])->name('usage');
        
    });
    
    // Rotas para demonstração e testes dos padrões
    Route::prefix('gof-patterns')->name('gof.')->group(function () {
        
        // Estatísticas dos padrões
        Route::get('/stats', [EnhancedItemController::class, 'getPatternStats'])->name('stats');
        
        // Demonstrações interativas
        Route::get('/demonstrate', [EnhancedItemController::class, 'demonstratePatterns'])->name('demonstrate');
        
        // Testes automatizados
        Route::get('/test', [EnhancedItemController::class, 'testPatterns'])->name('test');
        
        // Rotas específicas para cada padrão
        Route::prefix('singleton')->name('singleton.')->group(function () {
            Route::get('/stats', function () {
                $db = \App\Patterns\Singleton\DatabaseConnection::getInstance();
                return response()->json($db->getStats());
            })->name('stats');
            
            Route::get('/test-connection', function () {
                $db = \App\Patterns\Singleton\DatabaseConnection::getInstance();
                return response()->json([
                    'connection_ok' => $db->testConnection(),
                    'stats' => $db->getStats()
                ]);
            })->name('test');
        });
        
        Route::prefix('factory')->name('factory.')->group(function () {
            Route::get('/types', function () {
                return response()->json(\App\Patterns\Factory\ItemFactory::getAvailableTypes());
            })->name('types');
            
            Route::get('/type-info/{category}', function ($category) {
                try {
                    $info = \App\Patterns\Factory\ItemFactory::getTypeInfo($category);
                    return response()->json($info);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            })->name('type-info');
            
            Route::post('/create-item', function (\Illuminate\Http\Request $request) {
                try {
                    $category = $request->input('category', 'camiseta');
                    $data = $request->input('data', []);
                    
                    $itemType = \App\Patterns\Factory\ItemFactory::createItemType($category, $data);
                    
                    return response()->json([
                        'type' => $itemType->getType(),
                        'characteristics' => $itemType->getCharacteristics(),
                        'validation_rules' => $itemType->getValidationRules(),
                        'care_instructions' => $itemType->getCareInstructions()
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            })->name('create');
        });
        
        Route::prefix('observer')->name('observer.')->group(function () {
            Route::get('/stats', function () {
                $subject = new \App\Patterns\Observer\EventSubject();
                $subject->attach(new \App\Patterns\Observer\Observers\ItemObserver());
                $subject->attach(new \App\Patterns\Observer\Observers\BudgetObserver());
                
                return response()->json($subject->getStats());
            })->name('stats');
            
            Route::post('/fire-event', function (\Illuminate\Http\Request $request) {
                $subject = new \App\Patterns\Observer\EventSubject();
                $subject->attach(new \App\Patterns\Observer\Observers\ItemObserver());
                $subject->attach(new \App\Patterns\Observer\Observers\BudgetObserver());
                
                $event = $request->input('event', 'test.event');
                $data = $request->input('data', ['test' => true]);
                
                $subject->notify($event, $data);
                
                return response()->json([
                    'event_fired' => $event,
                    'data' => $data,
                    'history' => $subject->getEventHistory(5)
                ]);
            })->name('fire-event');
        });
        
        Route::prefix('strategy')->name('strategy.')->group(function () {
            Route::get('/filters', function () {
                $context = new \App\Patterns\Strategy\FilterContext();
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\CategoryFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\ColorFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\SeasonFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\ConditionFilter());
                
                return response()->json($context->getRegisteredStrategies());
            })->name('filters');
            
            Route::post('/apply-filter', function (\Illuminate\Http\Request $request) {
                $context = new \App\Patterns\Strategy\FilterContext();
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\CategoryFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\ColorFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\SeasonFilter());
                $context->registerStrategy(new \App\Patterns\Strategy\Filters\ConditionFilter());
                
                $filterName = $request->input('filter');
                $value = $request->input('value');
                
                try {
                    $context->addFilter($filterName, $value);
                    
                    // Simula aplicação em query
                    $query = \App\Models\Item::query();
                    $originalSql = $query->toSql();
                    
                    $context->applyFilters($query);
                    $filteredSql = $query->toSql();
                    
                    return response()->json([
                        'filter_applied' => $filterName,
                        'value' => $value,
                        'sql_changed' => $originalSql !== $filteredSql,
                        'active_filters' => $context->getActiveFilters()
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            })->name('apply');
        });
        
        Route::prefix('decorator')->name('decorator.')->group(function () {
            Route::get('/decorators', function () {
                $manager = new \App\Patterns\Decorator\ItemDecoratorManager();
                return response()->json($manager->getRegisteredDecorators());
            })->name('list');
            
            Route::post('/decorate-item', function (\Illuminate\Http\Request $request) {
                $manager = new \App\Patterns\Decorator\ItemDecoratorManager();
                
                $itemData = $request->input('item', [
                    'id' => 1,
                    'name' => 'Item de Teste',
                    'is_favorite' => true,
                    'usage_count' => 15,
                    'purchase_price' => 120,
                    'condition' => 'usado_bom'
                ]);
                
                $decorators = $request->input('decorators', null);
                
                $decoratedItem = $manager->decorateItem($itemData, $decorators);
                
                return response()->json([
                    'original_item' => $itemData,
                    'decorated_item' => $decoratedItem,
                    'decorations_added' => isset($decoratedItem['decorations']),
                    'metadata' => $decoratedItem['decoration_metadata'] ?? null
                ]);
            })->name('decorate');
        });
        
    });
    
});

// Rota para dashboard dos padrões GoF (se necessário)
Route::get('/gof-dashboard', function () {
    return view('gof.dashboard');
})->middleware('auth')->name('gof.dashboard');
