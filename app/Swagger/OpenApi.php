<?php

declare(strict_types=1);

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Modular Laravel API",
 *         version="1.0.0",
 *         description="API documentation for Modular Laravel application"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object"
 * )
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object"
 * )
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management"
 * )
 * @OA\Tag(
 *     name="Roles",
 *     description="Role management"
 * )
 * @OA\Tag(
 *     name="Permissions",
 *     description="Permission management"
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/auth/register",
 *     @OA\Post(
 *         operationId="authRegister",
 *         tags={"Authentication"},
 *         summary="Register",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/auth/login",
 *     @OA\Post(
 *         operationId="authLogin",
 *         tags={"Authentication"},
 *         summary="Login",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/auth/logout",
 *     @OA\Post(
 *         operationId="authLogout",
 *         tags={"Authentication"},
 *         summary="Logout",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/auth/me",
 *     @OA\Get(
 *         operationId="authMe",
 *         tags={"Authentication"},
 *         summary="Current user",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/auth/forgot-password",
 *     @OA\Post(
 *         operationId="authForgotPassword",
 *         tags={"Authentication"},
 *         summary="Forgot password",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/auth/reset-password",
 *     @OA\Post(
 *         operationId="authResetPassword",
 *         tags={"Authentication"},
 *         summary="Reset password",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/users",
 *     @OA\Get(
 *         operationId="usersIndex",
 *         tags={"Users"},
 *         summary="List users",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/users/{id}",
 *     @OA\Get(
 *         operationId="usersShow",
 *         tags={"Users"},
 *         summary="Get user",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/roles",
 *     @OA\Get(
 *         operationId="rolesIndex",
 *         tags={"Roles"},
 *         summary="List roles",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/roles/{id}",
 *     @OA\Get(
 *         operationId="rolesShow",
 *         tags={"Roles"},
 *         summary="Get role",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/permissions",
 *     @OA\Get(
 *         operationId="permissionsIndex",
 *         tags={"Permissions"},
 *         summary="List permissions",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/v1/permissions/{id}",
 *     @OA\Get(
 *         operationId="permissionsShow",
 *         tags={"Permissions"},
 *         summary="Get permission",
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 */
final class OpenApi
{
}
