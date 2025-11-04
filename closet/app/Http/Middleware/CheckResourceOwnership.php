<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Item;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Memory;
use App\Models\Favorite;

class CheckResourceOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $model = null): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Mapear modelos para verificação
        $modelMap = [
            'item' => Item::class,
            'category' => Category::class,
            'budget' => Budget::class,
            'memory' => Memory::class,
            'favorite' => Favorite::class,
        ];

        if ($model && isset($modelMap[$model])) {
            $modelClass = $modelMap[$model];
            $resourceId = $request->route()->parameter($model);
            
            if ($resourceId) {
                $resource = $modelClass::find($resourceId);
                
                if (!$resource) {
                    abort(404, 'Recurso não encontrado.');
                }
                
                // Verificar se o recurso pertence ao usuário
                if ($resource->user_id !== $user->id) {
                    abort(403, 'Acesso negado. Este recurso não pertence a você.');
                }
            }
        }

        return $next($request);
    }
}
