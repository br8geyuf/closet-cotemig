<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user()->loadCount([
            'followers', 'following', 'items', 'categories', 'favorites', 'memories', 'promotions',
        ]);

        $postsCount = method_exists($user, 'posts') ? $user->posts()->count() : 0;

        return view('dashboard', compact('user', 'postsCount'));
    }

    public function search(Request $request)
    {
        $query  = $request->string('q')->toString();
        $filter = $request->string('filter')->toString();
        $sort   = $request->string('sort')->toString();

        $users = User::query();

        if ($query) {
            $users->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        if ($filter === 'seguindo' && Auth::check()) {
            $users->whereIn('id', Auth::user()->following()->pluck('followed_id'));
        }

        if ($filter === 'recentes') $users->orderByDesc('created_at');

        if ($sort === 'name_asc') $users->orderBy('name');
        if ($sort === 'name_desc') $users->orderByDesc('name');
        if ($sort === 'followers_asc') $users->withCount('followers')->orderBy('followers_count');
        if ($sort === 'followers_desc') $users->withCount('followers')->orderByDesc('followers_count');

        $paginated = $users->paginate(10, ['id', 'name', 'email']);

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'has_more' => $paginated->hasMorePages(),
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile();
        
        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'bio' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Atualizar dados básicos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Atualizar senha
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'A senha atual está incorreta.'])->withInput();
            }
            
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        // Upload de avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();

            if ($user->profile && $user->profile->avatar) {
                $oldPath = 'public/avatars/' . $user->profile->avatar;
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            $file->storeAs('public/avatars', $filename);

            $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
            $profile->avatar = $filename;
            $profile->save();
        }

        // Atualizar ou criar perfil
        $profileData = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'bio' => $request->bio,
        ];

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            UserProfile::create($profileData);
        }

        return redirect()->route('dashboard')->with('success', '✅ Perfil atualizado com sucesso!');
    }

    public function show(int $id)
    {
        $user = User::with('profile')->findOrFail($id);
        $isFollowing = Auth::check() && Auth::user()->following()->where('followed_id', $id)->exists();
        return view('users.show', compact('user', 'isFollowing'));
    }

    /**
     * ✅ Seguir usuário + notificação
     */
    public function follow(int $id)
    {
        $target = User::findOrFail($id);
        $current = Auth::user();

        if ($current->id === $target->id) {
            return back()->with('error', '❌ Você não pode seguir a si mesmo.');
        }

        // Evita seguir duplicado
        if ($current->following()->where('followed_id', $target->id)->exists()) {
            return back()->with('info', "Você já está seguindo {$target->name}.");
        }

        // Seguir
        $current->following()->attach($target->id);

        // 🔔 Enviar notificação
        $target->notifyNewFollower($current);

        return back()->with('success', "✅ Agora você está seguindo {$target->name}");
    }

    public function unfollow(int $id)
    {
        $target = User::findOrFail($id);
        Auth::user()->following()->detach($target->id);
        return back()->with('success', "🚫 Você deixou de seguir {$target->name}");
    }
}
