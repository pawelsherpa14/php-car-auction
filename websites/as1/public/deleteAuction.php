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


if (isset($_GET['delete'])) {
    $auctionId = $_GET['delete'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT userId FROM auction WHERE id = ?");
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($auction && $auction['userId'] == $userId) {
        try {
            
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM bid WHERE auctionId = ?");
            $stmt->execute([$auctionId]);

            $stmt = $pdo->prepare("DELETE FROM auction WHERE id = ? AND userId = ?");
            $stmt->execute([$auctionId, $userId]);

            $pdo->commit();
            $success = "Auction and its bids deleted successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error deleting auction: " . $e->getMessage();
        }
    } else {
        $error = "You can only delete your own auctions.";
    }
}
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.description, a.endDate, c.name AS categoryName 
    FROM auction a 
    JOIN category c ON a.categoryId = c.id 
    WHERE a.userId = ?
    ORDER BY a.endDate ASC
");
$stmt->execute([$userId]);
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<main>
    <h1>Your Auctions</h1>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (empty($auctions)): ?>
        <p>You have not posted any auctions yet. <a href="addAuction.php">Add one now!</a></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Title</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Category</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">End Date</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auctions as $auction): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($auction['title']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($auction['categoryName']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($auction['endDate']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <a href="editAuction.php?id=<?php echo $auction['id']; ?>" style="color: blue;">Edit</a> | 
                            <a href="deleteAuction.php?delete=<?php echo $auction['id']; ?>" 
                               style="color: red;" 
                               onclick="return confirm('Are you sure you want to delete this auction?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php ?>