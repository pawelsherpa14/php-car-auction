<?php
session_start();

try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($searchTerm)) {
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.description, a.endDate, a.image, c.name AS categoryName, u.name AS userName
        FROM auction a
        JOIN category c ON a.categoryId = c.id
        JOIN user u ON a.userId = u.id
        WHERE a.title LIKE ? OR a.description LIKE ?
        ORDER BY a.id DESC
    ");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $auctions = [];
}

include 'header.php';
?>

<main>
    <h1>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h1>
    <?php if (empty($searchTerm)): ?>
        <p>Please enter a search term.</p>
    <?php elseif (empty($auctions)): ?>
        <p>No auctions found.</p>
    <?php else: ?>
        <div class="auction-list">
            <?php foreach ($auctions as $auction): ?>
                <div class="auction-item">
                    <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                    <?php if (!empty($auction['image'])): ?>
                        <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="Auction Image" width="100" onerror="this.style.display='none'">
                    <?php endif; ?>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($auction['categoryName']); ?></p>
                    <p><?php echo htmlspecialchars(substr($auction['description'], 0, 100)) . '...'; ?></p>
                    <p><strong>Posted by:</strong> <?php echo htmlspecialchars($auction['userName']); ?></p>
                    <p><strong>Ends:</strong> <?php echo htmlspecialchars($auction['endDate']); ?></p>
                    <a class="moreauctionLink" href="auction.php?id=<?php echo $auction['id']; ?>">More</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a href="index.php">Back to All Auctions</a>
</main>

<?php  ?>