<?php
session_start();

try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $name = $_POST['name'];

    try {
        $stmt = $pdo->prepare("INSERT INTO user (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute([$email, $password, $name]);
        echo "Registration successful! <a href='login.php'>Log in here</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Car Auction</title>
</head>
<body>
    <main>
    <?php if (isset($success)) echo "<p>$success</p>"; ?>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <h1>Register</h1>
        <form method="POST" action="">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="name">Display Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Log in</a></p>
    </main>
</body>
</html>