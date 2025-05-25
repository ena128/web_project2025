<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class AuthMiddleware {
    
   public function verifyToken($token){
       if(!$token)
           Flight::halt(401, "Missing auth header");
           
       // Handle Bearer prefix
       if (strpos($token, 'Bearer ') === 0) {
           $token = substr($token, 7);
       }
       
       $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
       Flight::set('user', $decoded_token->user);
       Flight::set('jwt_token', $token);
       return TRUE;
   }

   public function authorizeRole($requiredRole) {
       $user = Flight::get('user');
       if ($user->role !== $requiredRole) {
           Flight::halt(403, 'Access denied: insufficient privileges');
       }
   }

   public function authorizeRoles($roles) {
       $user = Flight::get('user');
       if (!in_array($user->role, $roles)) {
           Flight::halt(403, 'Forbidden: role not allowed');
       }
   }

   function authorizePermission($permission) {
       $user = Flight::get('user');
       if (!in_array($permission, $user->permissions)) {
           Flight::halt(403, 'Access denied: permission missing');
       }
   }   
   
   /**
    * Validates request data against specified rules
    * @param array $rules Key-value pairs of field names and validation rules
    * @return bool True if validation passes, exits with 422 response if fails
    */
   public function validateRequest($rules) {
       $request = Flight::request();
       $data = $request->data->getData();
       $errors = [];
       
       foreach ($rules as $field => $rule) {
           // Parse rules (separated by |)
           $ruleSet = explode('|', $rule);
           
           foreach ($ruleSet as $singleRule) {
               // Check required fields
               if ($singleRule === 'required' && (!isset($data[$field]) || $data[$field] === '')) {
                   $errors[$field] = "Field is required";
                   break; // Skip other validations if required field is missing
               }
               
               // Skip validation if field is empty and not required
               if (!isset($data[$field]) || $data[$field] === '') {
                   continue;
               }
               
               // Email validation
               if ($singleRule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                   $errors[$field] = "Invalid email format";
               }
               
               // Min length validation
               if (strpos($singleRule, 'min:') === 0) {
                   $min = (int)substr($singleRule, 4);
                   if (strlen($data[$field]) < $min) {
                       $errors[$field] = "Minimum length is $min characters";
                   }
               }
           }
       }
       
       if (!empty($errors)) {
           Flight::json(['status' => 'error', 'errors' => $errors], 422);
           return false;
       }
       
       return true;
   }
   
 
   /**
    * Hash a password securely using PHP's built-in function
    * @param string $password Plain text password
    * @return string Hashed password
    */
   public function hashPassword($password) {
       return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
   }
   
   /**
    * Verify a password against a hash
    * @param string $password Plain text password
    * @param string $hash Stored hash
    * @return bool True if password matches
    */
   public function verifyPassword($password, $hash) {
       return password_verify($password, $hash);
   }

   /**
    * Simple logging function 
    * @param string $message The message to log
    * @param string $level Log level (info, warning, error)
    */
   public function log($message, $level = 'info') {
       $logDir = __DIR__ . '/../logs';
       
       // Create logs directory if it doesn't exist
       if (!is_dir($logDir)) {
           mkdir($logDir, 0755, true);
       }
       
       $logFile = $logDir . '/app.log';
       $timestamp = date('Y-m-d H:i:s');
       $user = Flight::get('user') ? Flight::get('user')->username : 'guest';
       $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
       
       $logMessage = "[$timestamp][$level][$ip][$user] $message" . PHP_EOL;
       file_put_contents($logFile, $logMessage, FILE_APPEND);
   }
   
   /**
    * Error handling wrapper for API endpoints
    * @param callable $callback The endpoint function to execute
    * @return callable A wrapped function with error handling
    */
   public function withErrorHandling($callback) {
       return function() use ($callback) {
           try {
               return call_user_func_array($callback, func_get_args());
           } catch (Exception $e) {
               // Log the error
               $this->log($e->getMessage(), 'error');
               
               // Determine appropriate status code
               $statusCode = 500;
               if ($e->getCode() >= 400 && $e->getCode() < 600) {
                   $statusCode = $e->getCode();
               }
               
               // Return consistent error response
               Flight::json([
                   'status' => 'error',
                   'message' => $e->getMessage()
               ], $statusCode);
           }
       };
   }
}