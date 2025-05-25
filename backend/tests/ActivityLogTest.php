<?php
use PHPUnit\Framework\TestCase;

class ActivityLogTest extends TestCase {

    private static $token;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../rest/index.php';

        // Create token manually or via login API
        $payload = [
            "user" => [
                "id" => 1,
                "email" => "admin@example.com",
                "role" => "admin",
                "permissions" => ["view_logs"]
            ],
            "iat" => time(),
            "exp" => time() + 3600 // 1 hour
        ];

        self::$token = \Firebase\JWT\JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
    }

    public function setUp(): void
    {
        $_SERVER['HTTP_AUTHENTICATION'] = self::$token;

        Flight::halt(false); // Don't exit
    }

    public function testGetAllActivityLogs()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/activitylogs';

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetActivityLogById()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/activitylogs/1'; // ensure ID 1 exists in DB

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
        $this->assertStringContainsString('"id":1', $output);
    }
}
