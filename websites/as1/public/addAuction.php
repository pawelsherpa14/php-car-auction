<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT id, name FROM category");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $categoryId = $_POST['category'];
    $endDate = $_POST['auctionEndDate'];
    $userId = $_SESSION['user_id'];

    if (empty($title) || empty($description) || empty($categoryId) || empty($endDate)) {
        $error = "All required fields must be filled.";
    } else {
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "images/auctions/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $imagePath = $targetDir . uniqid() . "_" . basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($_FILES['image']['size'] > 5000000) { 
                $error = "Image size must be less than 5MB.";
            } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $error = "Failed to upload image.";
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO auction (title, description, categoryId, endDate, userId, image) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $description, $categoryId, $endDate, $userId, $imagePath]);
                $success = "Auction added successfully! <a href='index.php'>View auctions</a>";
            } catch (PDOException $e) {
                $error = "Error adding auction: " . $e->getMessage();
            }
        }
    }
}

include 'header.php';

?>

<main>
    <h1>Add a New Auction</h1>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="5" ></textarea><br><br>

        <label for="category">Category:</label><br>
        <select id="category" name="category" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="auctionEndDate">End Date/Time:</label><br>
        <input type="datetime-local" id="auctionEndDate" name="auctionEndDate" required><br><br>

        <label for="image">Auction Image :</label><br>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <input type="submit" value="Add Auction">
    </form>

    <br><br><h1>Delete your auction?</h1>
    <a href="deleteAuction.php" style="color:red;">Delete Auction</a>

</main>

<?php

?>