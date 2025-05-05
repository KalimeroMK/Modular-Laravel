<?php

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Modules\Auth\Exceptions\AuthNotFoundException;
use App\Modules\Auth\Exceptions\AuthStoreException;
use App\Modules\Auth\Http\Actions\CreateAuthAction;
use App\Modules\Auth\Http\Actions\FindAuthByEmailAction;
use App\Modules\Auth\Http\Actions\GetAuthenticatedUserAction;
use App\Modules\Auth\Http\Actions\LogoutAuthAction;
use App\Modules\Auth\Http\Actions\ResetPasswordAction;
use App\Modules\Auth\Http\Actions\SendPasswordResetLinkEmailAction;
use App\Modules\Auth\Http\DTOs\CreateAuthDTO;
use App\Modules\Auth\Http\Requests\CreateAuthRequest;
use App\Modules\Auth\Http\Requests\LoginAuthRequest;
use App\Modules\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @return JsonResponse
     *
     * @throws AuthStoreException
     */
    public function signup(CreateAuthRequest $request)
    {
        $dto = CreateAuthDTO::fromArray($request->validated());
        $result = app(CreateAuthAction::class)->execute($dto);

        return $result
            ? response()->json('User created successfully', 200)
            : response()->json(null, 404);
    }

    /**
     * @return JsonResponse
     *
     * @throws AuthNotFoundException
     */
    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->validated();
        $user = app(FindAuthByEmailAction::class)->execute($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken($credentials['email'])->plainTextToken,
        ]);
    }

    /*
     * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
    */
    public function logout(Request $request)
    {
        app(LogoutAuthAction::class)->execute($request);

        return response()->json(null, 200);
    }

    /*
     * Get authenticated user details
    */
    public function getAuthenticatedUser(Request $request)
    {
        return app(GetAuthenticatedUserAction::class)->execute($request);
    }

    public function sendPasswordResetLinkEmail(Request $request)
    {
        $result = app(SendPasswordResetLinkEmailAction::class)->execute($request);

        return response()->json($result, 200);
    }

    public function resetPassword(Request $request)
    {
        $result = app(ResetPasswordAction::class)->execute($request);

        return response()->json($result, 200);
    }
}
