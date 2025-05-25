<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    private static $token;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../rest/index.php';
    }

    public function setUp(): void
    {
        Flight::halt(false);
        unset($_SERVER['HTTP_AUTHENTICATION']); // Unset token for public routes
    }

    private function mockJsonInput(array $data): void
    {
        $json = json_encode($data);
        file_put_contents('php://memory', $json); // Not needed, just for reference
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', MockPhpStream::class);
        file_put_contents('php://input', $json);
    }

    public function testGetAllUsers()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users';

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
        $_SERVER['HTTP_AUTHENTICATION'] = self::$token;

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetUserById()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users/1';

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
        $_SERVER['HTTP_AUTHENTICATION'] = self::$token;

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
        $this->assertStringContainsString('"id":1', $output);
    }

    public function testRegisterUser()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/auth/register';

        $this->mockJsonInput([
            'email' => 'testuser@example.com',
            'password' => 'password123'
        ]);

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertTrue(in_array(http_response_code(), [200, 201, 400]), 'Expected 201 or 400 if user exists');
        $this->assertJson($output);
    }

    public function testLoginUser()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/auth/login';

        $this->mockJsonInput([
            'email' => 'testuser@example.com',
            'password' => 'password123'
        ]);

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
        $this->assertStringContainsString('token', $output);
    }

    public static function tearDownAfterClass(): void
    {
        stream_wrapper_restore('php');
    }
}

class MockPhpStream {
    private $index = 0;
    private $data;
    private $position;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->data = file_get_contents("php://memory");
        $this->position = 0;
        return true;
    }

    public function stream_read($count)
    {
        $result = substr($this->data, $this->position, $count);
        $this->position += strlen($result);
        return $result;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->data);
    }

    public function stream_stat()
    {
        return [];
    }
}
