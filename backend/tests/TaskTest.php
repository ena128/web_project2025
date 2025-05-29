<?php
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase {

    private static $token;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../rest/index.php';

        $payload = [
            "user" => [
                "id" => 1,
                "email" => "admin@example.com",
                "role" => "admin"
            ],
            "iat" => time(),
            "exp" => time() + 3600
        ];

        self::$token = \Firebase\JWT\JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
    }

    public function setUp(): void
    {
        $_SERVER['HTTP_AUTHENTICATION'] = self::$token;
        Flight::halt(false);
    }

    public function testGetAllTasks()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tasks';

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code(), "Expected HTTP 200 OK");
        $this->assertJson($output, "Response is not valid JSON");
    }

    public function testGetTaskById()
    {
        $testId = 1; // Ensure this ID exists in the database

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/tasks/{$testId}";

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code(), "Expected HTTP 200 OK");
        $this->assertJson($output, "Response is not valid JSON");
        $this->assertStringContainsString("\"id\":{$testId}", $output, "Response should contain task ID");
    }
}
