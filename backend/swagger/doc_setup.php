<?php
/**
 * @OA\Info(
 *   title="API",
 *   description="Web programming API",
 *   version="1.0",
 *   @OA\Contact(
 *     email="web2001programming@gmail.com",
 *     name="Web Programming"
 *   )
 * ),
 * @OA\Server(
 *     url="http://localhost/web_2025/backend/rest/",
 *     description="API server"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */