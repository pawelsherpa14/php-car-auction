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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: deleteAuction.php"); 
    exit;
}

$auctionId = $_GET['id'];
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.title, a.description, a.categoryId, a.endDate, a.image, a.userId 
    FROM auction a 
    WHERE a.id = ? AND a.userId = ?
");
$stmt->execute([$auctionId, $userId]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    $error = "Auction not found or you do not have permission to edit it.";
    include 'header.php';
    echo "<main><p style='color: red;'>$error</p><p><a href='deleteAuction.php'>Back to your auctions</a></p></main>";
    include 'footer.php';
    exit;
}

$stmt = $pdo->query("SELECT id, name FROM category");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $categoryId = $_POST['category'];
    $endDate = $_POST['auctionEndDate'];

    if (empty($title) || empty($description) || empty($categoryId) || empty($endDate)) {
        $error = "All required fields must be filled.";
    } else {
        $imagePath = $auction['image']; 
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
            } else {
                if ($auction['image'] && file_exists($auction['image'])) {
                    unlink($auction['image']);
                }
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE auction 
                    SET title = ?, description = ?, categoryId = ?, endDate = ?, image = ? 
                    WHERE id = ? AND userId = ?
                ");
                $stmt->execute([$title, $description, $categoryId, $endDate, $imagePath, $auctionId, $userId]);
                $success = "Auction updated successfully! <a href='deleteAuction.php'>Back to your auctions</a>";
            } catch (PDOException $e) {
                $error = "Error updating auction: " . $e->getMessage();
            }
        }
    }
}

include 'header.php';
?>

<main>
    <h1>Edit Auction</h1>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($auction['title']); ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($auction['description']); ?></textarea><br><br>

        <label for="category">Category:</label><br>
        <select id="category" name="category" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" 
                        <?php echo $category['id'] == $auction['categoryId'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="auctionEndDate">End Date/Time:</label><br>
        <input type="datetime-local" id="auctionEndDate" name="auctionEndDate" 
               value="<?php echo date('Y-m-d\TH:i', strtotime($auction['endDate'])); ?>" required><br><br>

        <label for="image">Auction Image (leave blank to keep current image):</label><br>
        <?php if ($auction['image']): ?>
            <p>Current Image: <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="Current Auction Image" style="max-width: 200px;"></p>
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <input type="submit" value="Update Auction">
    </form>
</main>

<?php ?>