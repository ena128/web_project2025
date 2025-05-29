<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/ActivityLogsService.php';

Flight::set('activityLogsService', new ActivityLogsService());

/**
 * @OA\Get(
 *     path="/logs",
 *     tags={"activityLogs"},
 *     summary="Get all activity logs",
 *     security={{"ApiKeyAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all activity logs",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 properties={
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="action", type="string", example="User logged in"),
 *                     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-04-17T12:00:00")
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */


Flight::route('GET /logs', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::get('activityLogsService')->getAll());
});

/**
 * @OA\Get(
 *     path="/logs/{id}",
 *     tags={"activityLogs"},
 *     summary="Get a specific activity log by ID",
 *     security={{"ApiKeyAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="The requested activity log",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="action", type="string", example="User logged in"),
 *                 @OA\Property(property="timestamp", type="string", format="date-time", example="2025-04-17T12:00:00")
 *             }
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('GET /logs/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::get('activityLogsService')->getById($id));
});

/**
 * @OA\Post(
 *     path="/logs",
 *     tags={"activityLogs"},
 *     summary="Create a new activity log",
 *     security={{"ApiKeyAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"action"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="action", type="string", example="User logged in"),
 *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-04-17T12:00:00")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Activity log created successfully"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('POST /logs', function() {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('activityLogsService')->create($data));
});

/**
 * @OA\Put(
 *     path="/logs/{id}",
 *     tags={"activityLogs"},
 *     summary="Update an existing activity log",
 *     security={{"ApiKeyAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"action"},
 *             @OA\Property(property="action", type="string", example="User logged out"),
 *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-04-17T13:00:00")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Activity log updated successfully"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('PUT /logs/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('activityLogsService')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/logs/{id}",
 *     tags={"activityLogs"},
 *     summary="Delete an activity log",
 *     security={{"ApiKeyAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Activity log deleted successfully"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
Flight::route('DELETE /logs/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::get('activityLogsService')->delete($id));
});
?>
