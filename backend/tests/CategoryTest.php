<?php
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase {

    private static $token;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../rest/index.php';

        // Create a JWT token for an admin or user with valid access
        $payload = [
            "user" => [
                "id" => 1,
                "email" => "admin@example.com",
                "role" => "admin",
                "permissions" => ["view_categories"]
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

    public function testGetAllCategories()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/categories';

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetCategoryById()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/categories/1'; // Make sure this category ID exists

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
        $this->assertStringContainsString('"id":1', $output);
    }
}
