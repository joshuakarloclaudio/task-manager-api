<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function register(RegisterUserRequest $request): UserResource
    {
        $data = $request->validated();

        $user = $this->repository->create($data);

        $request->session()->regenerate();
        Auth::login($user);

        return new UserResource($user);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json($request->user());
        }

        return response()->json([
            'message' => __('auth.failed')
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function profile(Request $request): User
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
