<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/UserService.php';

Flight::set('UserService', new UserService());

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     @OA\Response(
 *         response=200,
 *         description="List of all users"
 *     )
 * )
 */
Flight::route('GET /users', function () {
    Flight::auth_middleware()->verifyToken();
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        Flight::json(Flight::get('UserService')->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /users/@id', function ($id) {
    Flight::auth_middleware()->verifyToken();
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    try {
        $user = Flight::get('UserService')->getById($id);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::json(['error' => 'User not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Create a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "role"},
 *             @OA\Property(property="name", type="string", example="Ena"),
 *             @OA\Property(property="email", type="string", example="eennaaaaa@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully"
 *     )
 * )
 */
Flight::route('POST /users', function () {
    Flight::auth_middleware()->verifyToken();
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        $data = Flight::request()->data->getData();
        $newUser = Flight::get('UserService')->create($data);
        Flight::json(['status' => 'success', 'message' => 'User created successfully', 'data' => $newUser], 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "role"},
 *             @OA\Property(property="name", type="string", example="Updated Ena"),
 *             @OA\Property(property="email", type="string", example="updated@example.com"),
 *             @OA\Property(property="password", type="string", example="newpass123"),
 *             @OA\Property(property="role", type="string", example="admin")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated"
 *     )
 * )
 */
Flight::route('PUT /users/@id', function ($id) {
    Flight::auth_middleware()->verifyToken();
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        $data = Flight::request()->data->getData();
        $updatedUser = Flight::get('UserService')->update($id, $data);
        if ($updatedUser) {
            Flight::json(['status' => 'success', 'message' => 'User updated successfully', 'data' => $updatedUser]);
        } else {
            Flight::json(['status' => 'error', 'message' => 'User not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /users/@id', function ($id) {
    Flight::auth_middleware()->verifyToken();
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        $result = Flight::get('UserService')->delete($id);
        if ($result) {
            Flight::json(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            Flight::json(['status' => 'error', 'message' => 'User not found'], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
