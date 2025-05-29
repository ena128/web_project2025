<?php
// Database connection
$dsn = "mysql:host=localhost;dbname=todomasterdb;charset=utf8mb4";
$username = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all users with their user_id and password
    $stmt = $pdo->query("SELECT user_id, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        // Check if password is already hashed (optional)
        if (!password_get_info($user['password'])['algo']) {
            $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
            $update->execute([':password' => $hashed, ':user_id' => $user['user_id']]);
            echo "Password for user_id {$user['user_id']} hashed successfully.\n";
        } else {
            echo "Password for user_id {$user['user_id']} already hashed.\n";
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
