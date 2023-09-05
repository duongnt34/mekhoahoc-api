<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $query = User::query();
            dump($query->get()->toArray());
            return UserResource::collection($query->get());
        }
        dump(1);
        return UserResource::collection(User::all());
    }

    public function store(AdminStoreUserRequest $request){
        dump($request->all());
        $validated = $request->validated();

        $user = User::create($validated);
        return new UserResource($user);
    }
}
