<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;

// Importa os padrões GoF implementados
use App\Patterns\Singleton\DatabaseConnection;
use App\Patterns\Factory\ItemFactory;
use App\Patterns\Observer\EventSubject;
use App\Patterns\Observer\Observers\ItemObserver;
use App\Patterns\Observer\Observers\BudgetObserver;
use App\Patterns\Strategy\FilterContext;
use App\Patterns\Strategy\Filters\CategoryFilter;
use App\Patterns\Strategy\Filters\ColorFilter;
use App\Patterns\Strategy\Filters\SeasonFilter;
use App\Patterns\Strategy\Filters\ConditionFilter;
use App\Patterns\Decorator\ItemDecoratorManager;

/**
 * Controller aprimorado que demonstra o uso dos padrões GoF
 * 
 * Este controller integra todos os padrões implementados:
 * - Singleton: Para conexão com banco
 * - Factory: Para criação de tipos específicos de itens
 * - Observer: Para eventos e notificações
 * - Strategy: Para filtros dinâmicos
 * - Decorator: Para enriquecimento de dados
 */
class EnhancedItemController extends Controller
{
    /**
     * Instância do Singleton para conexão com banco
     */
    private DatabaseConnection $dbConnection;

    /**
     * Subject para padrão Observer
     */
    private EventSubject $eventSubject;

    /**
     * Contexto para padrão Strategy (filtros)
     */
    private FilterContext $filterContext;

    /**
     * Gerenciador para padrão Decorator
     */
    private ItemDecoratorManager $decoratorManager;

    /**
     * Construtor que inicializa todos os padrões
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->initializePatterns();
    }

    /**
     * Inicializa todos os padrões GoF
     */
    private function initializePatterns(): void
    {
        // Singleton: Conexão com banco
        $this->dbConnection = DatabaseConnection::getInstance();

        // Observer: Sistema de eventos
        $this->eventSubject = new EventSubject();
        $this->eventSubject->attach(new ItemObserver());
        $this->eventSubject->attach(new BudgetObserver());

        // Strategy: Filtros dinâmicos
        $this->filterContext = new FilterContext();
        $this->filterContext->registerStrategy(new CategoryFilter());
        $this->filterContext->registerStrategy(new ColorFilter());
        $this->filterContext->registerStrategy(new SeasonFilter());
        $this->filterContext->registerStrategy(new ConditionFilter());

        // Decorator: Enriquecimento de dados
        $this->decoratorManager = new ItemDecoratorManager();
    }

    /**
     * Lista itens com filtros avançados e decorações
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // Inicia query base
        $query = Item::where('user_id', $userId);

        // Aplica filtros usando Strategy pattern
        $this->applyFilters($request, $query);

        // Executa query
        $items = $query->get()->toArray();

        // Aplica decorações usando Decorator pattern
        $decoratedItems = $this->decoratorManager->decorateItems($items);

        // Obtém categorias para filtros
        $categories = Category::where('user_id', $userId)->get();

        // Obtém estatísticas da conexão (Singleton)
        $dbStats = $this->dbConnection->getStats();

        return view('items.enhanced-index', compact(
            'decoratedItems', 
            'categories', 
            'dbStats'
        ));
    }

    /**
     * Cria um novo item usando Factory pattern
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'condition' => 'required|in:novo,usado_excelente,usado_bom,usado_regular,danificado',
            'colors' => 'nullable|array',
            'season' => 'required|in:primavera,verao,outono,inverno,todas',
            'occasion' => 'required|in:casual,trabalho,festa,esporte,formal,todas',
        ]);

        try {
            // Obtém categoria para determinar tipo
            $category = Category::find($validatedData['category_id']);
            
            // Factory: Cria tipo específico de item
            $itemType = ItemFactory::createItemType($category->name, $validatedData);
            
            // Processa dados específicos do tipo
            $processedData = $itemType->processData($validatedData);
            $processedData['user_id'] = Auth::id();

            // Singleton: Usa conexão única para transação
            $this->dbConnection->beginTransaction();

            // Cria o item
            $item = Item::create($processedData);

            // Observer: Dispara evento de criação
            $this->eventSubject->notify('item.created', [
                'item_id' => $item->id,
                'user_id' => $item->user_id,
                'category' => $category->name,
                'type' => $itemType->getType(),
                'characteristics' => $itemType->getCharacteristics()
            ]);

            $this->dbConnection->commit();

            return redirect()->route('enhanced-items.index')
                ->with('success', 'Item criado com sucesso usando padrões GoF!');

        } catch (\Exception $e) {
            $this->dbConnection->rollback();
            
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao criar item: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Adiciona item aos favoritos
     */
    public function addToFavorites(Request $request, $itemId)
    {
        $item = Item::where('id', $itemId)
                   ->where('user_id', Auth::id())
                   ->firstOrFail();

        // Atualiza item
        $item->update(['is_favorite' => true, 'favorited_at' => now()]);

        // Observer: Dispara evento de favorito
        $this->eventSubject->notify('item.favorited', [
            'item_id' => $item->id,
            'user_id' => $item->user_id,
            'category' => $item->category->name ?? 'unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado aos favoritos!'
        ]);
    }

    /**
     * Registra uso do item
     */
    public function recordUsage(Request $request, $itemId)
    {
        $item = Item::where('id', $itemId)
                   ->where('user_id', Auth::id())
                   ->firstOrFail();

        // Atualiza contador de uso
        $item->increment('usage_count');
        $item->update(['last_worn' => now()]);

        // Observer: Dispara evento de uso
        $this->eventSubject->notify('item.worn', [
            'item_id' => $item->id,
            'user_id' => $item->user_id,
            'category' => $item->category->name ?? 'unknown',
            'worn_date' => now()->toDateString(),
            'usage_count' => $item->usage_count
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Uso registrado com sucesso!',
            'usage_count' => $item->usage_count
        ]);
    }

    /**
     * Obtém informações detalhadas de um item (com decorações)
     */
    public function show($itemId)
    {
        $item = Item::where('id', $itemId)
                   ->where('user_id', Auth::id())
                   ->with('category')
                   ->firstOrFail();

        // Decorator: Aplica todas as decorações
        $decoratedItem = $this->decoratorManager->decorateItem($item->toArray());

        // Factory: Obtém informações do tipo
        $category = $item->category;
        $typeInfo = ItemFactory::getTypeInfo($category->name);

        return response()->json([
            'item' => $decoratedItem,
            'type_info' => $typeInfo,
            'db_stats' => $this->dbConnection->getStats()
        ]);
    }

    /**
     * Aplica filtros usando Strategy pattern
     */
    private function applyFilters(Request $request, $query): void
    {
        // Limpa filtros anteriores
        $this->filterContext->clearFilters();

        // Adiciona filtros baseados na requisição
        if ($request->filled('category')) {
            $this->filterContext->addFilter('category', $request->input('category'));
        }

        if ($request->filled('colors')) {
            $this->filterContext->addFilter('color', $request->input('colors'));
        }

        if ($request->filled('season')) {
            $this->filterContext->addFilter('season', $request->input('season'));
        }

        if ($request->filled('condition')) {
            $this->filterContext->addFilter('condition', $request->input('condition'));
        }

        // Aplica todos os filtros à query
        $this->filterContext->applyFilters($query);
    }

    /**
     * Obtém estatísticas dos padrões implementados
     */
    public function getPatternStats()
    {
        return response()->json([
            'singleton_stats' => $this->dbConnection->getStats(),
            'observer_stats' => $this->eventSubject->getStats(),
            'strategy_stats' => $this->filterContext->getStats(),
            'decorator_stats' => $this->decoratorManager->getStats(),
            'factory_types' => ItemFactory::getAvailableTypes()
        ]);
    }

    /**
     * Demonstra uso avançado dos padrões
     */
    public function demonstratePatterns()
    {
        $demonstrations = [];

        // Demonstração Singleton
        $demonstrations['singleton'] = [
            'description' => 'Demonstra que sempre obtemos a mesma instância',
            'instance1_id' => spl_object_id($this->dbConnection),
            'instance2_id' => spl_object_id(DatabaseConnection::getInstance()),
            'are_same' => $this->dbConnection === DatabaseConnection::getInstance(),
            'stats' => $this->dbConnection->getStats()
        ];

        // Demonstração Factory
        $demonstrations['factory'] = [
            'description' => 'Cria diferentes tipos de itens',
            'clothing_type' => ItemFactory::getTypeInfo('camiseta'),
            'shoe_type' => ItemFactory::getTypeInfo('tenis'),
            'accessory_type' => ItemFactory::getTypeInfo('cinto'),
            'available_types' => ItemFactory::getAvailableTypes()
        ];

        // Demonstração Observer
        $demonstrations['observer'] = [
            'description' => 'Sistema de eventos e observadores',
            'registered_observers' => $this->eventSubject->getObservers(),
            'event_history' => $this->eventSubject->getEventHistory(5),
            'stats' => $this->eventSubject->getStats()
        ];

        // Demonstração Strategy
        $demonstrations['strategy'] = [
            'description' => 'Filtros dinâmicos aplicáveis',
            'registered_strategies' => $this->filterContext->getRegisteredStrategies(),
            'active_filters' => $this->filterContext->getActiveFilters(),
            'stats' => $this->filterContext->getStats()
        ];

        // Demonstração Decorator
        $demonstrations['decorator'] = [
            'description' => 'Decoradores disponíveis para enriquecer itens',
            'registered_decorators' => $this->decoratorManager->getRegisteredDecorators(),
            'stats' => $this->decoratorManager->getStats()
        ];

        return response()->json($demonstrations);
    }

    /**
     * Testa todos os padrões com dados de exemplo
     */
    public function testPatterns()
    {
        $results = [];

        try {
            // Teste Singleton
            $results['singleton_test'] = $this->testSingleton();

            // Teste Factory
            $results['factory_test'] = $this->testFactory();

            // Teste Observer
            $results['observer_test'] = $this->testObserver();

            // Teste Strategy
            $results['strategy_test'] = $this->testStrategy();

            // Teste Decorator
            $results['decorator_test'] = $this->testDecorator();

            $results['overall_status'] = 'success';
            $results['message'] = 'Todos os padrões GoF funcionando corretamente!';

        } catch (\Exception $e) {
            $results['overall_status'] = 'error';
            $results['message'] = 'Erro nos testes: ' . $e->getMessage();
        }

        return response()->json($results);
    }

    /**
     * Testa o padrão Singleton
     */
    private function testSingleton(): array
    {
        $instance1 = DatabaseConnection::getInstance();
        $instance2 = DatabaseConnection::getInstance();
        
        return [
            'same_instance' => $instance1 === $instance2,
            'connection_test' => $instance1->testConnection(),
            'stats' => $instance1->getStats()
        ];
    }

    /**
     * Testa o padrão Factory
     */
    private function testFactory(): array
    {
        $clothingItem = ItemFactory::createItemType('camiseta', ['name' => 'Teste']);
        $shoeItem = ItemFactory::createItemType('tenis', ['name' => 'Teste']);
        
        return [
            'clothing_type' => $clothingItem->getType(),
            'shoe_type' => $shoeItem->getType(),
            'different_types' => $clothingItem->getType() !== $shoeItem->getType(),
            'clothing_characteristics' => $clothingItem->getCharacteristics(),
            'shoe_characteristics' => $shoeItem->getCharacteristics()
        ];
    }

    /**
     * Testa o padrão Observer
     */
    private function testObserver(): array
    {
        $initialEventCount = count($this->eventSubject->getEventHistory());
        
        // Dispara evento de teste
        $this->eventSubject->notify('test.event', ['test' => true]);
        
        $finalEventCount = count($this->eventSubject->getEventHistory());
        
        return [
            'event_fired' => $finalEventCount > $initialEventCount,
            'observers_count' => count($this->eventSubject->getObservers()),
            'latest_event' => $this->eventSubject->getEventHistory(1)[0] ?? null
        ];
    }

    /**
     * Testa o padrão Strategy
     */
    private function testStrategy(): array
    {
        // Cria query de teste
        $query = Item::query();
        $initialSql = $query->toSql();
        
        // Aplica filtro
        $this->filterContext->clearFilters();
        $this->filterContext->addFilter('condition', 'novo');
        $this->filterContext->applyFilters($query);
        
        $filteredSql = $query->toSql();
        
        return [
            'filter_applied' => $initialSql !== $filteredSql,
            'active_filters' => $this->filterContext->getActiveFilters(),
            'strategies_count' => count($this->filterContext->getRegisteredStrategies())
        ];
    }

    /**
     * Testa o padrão Decorator
     */
    private function testDecorator(): array
    {
        $testItem = [
            'id' => 999,
            'name' => 'Item de Teste',
            'is_favorite' => true,
            'usage_count' => 10,
            'purchase_price' => 100
        ];
        
        $decoratedItem = $this->decoratorManager->decorateItem($testItem);
        
        return [
            'has_decorations' => isset($decoratedItem['decorations']),
            'decorators_applied' => $decoratedItem['decoration_metadata']['decorators_applied'] ?? 0,
            'original_keys' => count($testItem),
            'decorated_keys' => count($decoratedItem)
        ];
    }
}
