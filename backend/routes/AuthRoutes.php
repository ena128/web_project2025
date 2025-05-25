<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::group('/auth', function() {
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register new user.",
     *     description="Add a new user to the database.",
     *     tags={"auth"},
     *     security={
     *         {"ApiKey": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Add new user",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"password", "email", "role"},
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="admin123",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="admin@gmail.com",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string",
     *                     example="ADMIN",
     *                     description="User role (e.g., ADMIN)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User has been added."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    Flight::route("POST /register", function () {
        // Validation rules like in snippet 1
        $validation_rules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];

        if (!Flight::auth_middleware()->validateRequest($validation_rules)) {
            return; // validateRequest handles error response
        }

        $data = Flight::request()->data->getData();

        // Log registration attempt
        Flight::auth_middleware()->log("Registration attempt for email: {$data['email']}", 'info');

        // Force ADMIN role regardless of input
        $data['role'] = 'ADMIN';

        $response = Flight::auth_service()->register($data);

        if ($response['success']) {
            Flight::auth_middleware()->log("User registered successfully: {$data['email']}", 'info');
            Flight::json([
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::auth_middleware()->log("Registration failed: {$response['error']}", 'error');
            Flight::halt(500, $response['error']);
        }
    });

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"auth"},
     *      summary="Login to system using email and password",
     *      @OA\Response(
     *           response=200,
     *           description="Student data and JWT"
     *      ),
     *      @OA\RequestBody(
     *          description="Credentials",
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", example="admin@gmail.com", description="Student email address"),
     *              @OA\Property(property="password", type="string", example="admin123", description="Student password")
     *          )
     *      )
     * )
     */
    Flight::route('POST /login', function() {
        // Validate login inputs
        $validation_rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        if (!Flight::auth_middleware()->validateRequest($validation_rules)) {
            return; // validateRequest handles error response
        }

        $data = Flight::request()->data->getData();

        // Log login attempt
        Flight::auth_middleware()->log("Login attempt for email: {$data['email']}", 'info');

        $response = Flight::auth_service()->login($data);

        if ($response['success']) {
            Flight::auth_middleware()->log("User logged in successfully: {$data['email']}", 'info');
            Flight::json([
                'message' => 'User logged in successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::auth_middleware()->log("Login failed for {$data['email']}", 'warning');
            Flight::halt(500, $response['error']);
        }
    });
});
