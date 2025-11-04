<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\User;
use App\Models\Item;

class ItemController extends Controller
{
    protected $itemRepository;
    protected $categoryRepository;

    public function __construct(
        ItemRepositoryInterface $itemRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->middleware('auth');
    }

    /**
     * Listar todos os itens do usuário
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filters = $request->all();

        $query = Item::where('user_id', $userId);

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $items = $query->get();
        $categories = $this->categoryRepository->findByUser($userId);

        return view('items.index', compact('items', 'categories', 'filters'));
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        $categories = $this->categoryRepository->findByUser(Auth::id());
        return view('items.create', compact('categories'));
    }

    /**
     * Armazenar novo item + notificar seguidores
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'brand'         => 'nullable|string|max:255',
            'size'          => 'nullable|string|max:50',
            'condition'     => 'required|in:novo,usado_excelente,usado_bom,usado_regular,danificado',
            'purchase_price'=> 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'season'        => 'required|in:primavera,verao,outono,inverno,todas',
            'occasion'      => 'required|in:casual,trabalho,festa,esporte,formal,todas',
            'colors'        => 'nullable|array',
            'tags'          => 'nullable|array',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Upload de imagens
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('items', 'public');
                $imagePaths[] = $path;
            }
        }
        $data['images'] = $imagePaths;

        // Cria o item
        $item = $this->itemRepository->create($data);

        // 🔔 Notificar seguidores
        $user = Auth::user();
        foreach ($user->followers as $follower) {
            $follower->notifyNewItem($user, $item);
        }

        return redirect()->route('items.index')
            ->with('success', '🛍️ Item adicionado com sucesso!');
    }
}
