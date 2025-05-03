<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/PriorityService.php';

Flight::set('priorityService', new PriorityService());

/**
 * @OA\Get(
 *     path="/priorities",
 *     tags={"priorities"},
 *     summary="Get all priorities",
 *     @OA\Response(
 *         response=200,
 *         description="List of all priorities"
 *     )
 * )
 */
Flight::route('GET /priorities', function() {
    try {
        Flight::json(Flight::get('priorityService')->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Get priority by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority found"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Priority not found"
 *     )
 * )
 */
Flight::route('GET /priorities/@id', function($id) {
    try {
        $priority = Flight::get('priorityService')->getById($id);
        if ($priority) {
            Flight::json($priority);
        } else {
            Flight::json(["status" => "error", "message" => "Priority not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/priorities",
 *     tags={"priorities"},
 *     summary="Create a new priority",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="High"),
 *             @OA\Property(property="color", type="string", example="#FF0000", default="#FF0000")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority created successfully"
 *     )
 * )
 */

// Create a new priority
Flight::route('POST /priorities', function() {
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::get('priorityService')->create($data));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Update a priority",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Updated Priority")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority updated successfully"
 *     )
 * )
 */
Flight::route('PUT /priorities/@id', function($id) {
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::get('priorityService')->update($id, $data));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/priorities/{id}",
 *     tags={"priorities"},
 *     summary="Delete a priority",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Priority ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Priority deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /priorities/@id', function($id) {
    try {
        Flight::json(Flight::get('priorityService')->delete($id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
