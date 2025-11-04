<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findWithProfile($id)
    {
        return $this->model->with('profile')->find($id);
    }

    public function createWithProfile(array $userData, array $profileData = [])
    {
        return DB::transaction(function () use ($userData, $profileData) {
            $user = $this->create($userData);
            
            if (!empty($profileData)) {
                $profileData['user_id'] = $user->id;
                UserProfile::create($profileData);
            }
            
            return $user->load('profile');
        });
    }
}

