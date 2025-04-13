<?php
session_start();

try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("
    SELECT 
        a.id, a.title, a.description, a.endDate, a.image, c.name AS categoryName, u.name AS userName,
        COALESCE((
            SELECT MAX(b.amount) 
            FROM bid b 
            WHERE b.auctionId = a.id
        ), 0) AS highestBid
    FROM auction a
    JOIN category c ON a.categoryId = c.id
    JOIN user u ON a.userId = u.id
    ORDER BY a.id DESC 
    LIMIT 10
");
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<main>
    <?php if (empty($auctions)): ?>
        <p>No auctions available yet.</p>
    <?php else: ?>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <?php if (!empty($auction['image'])): ?>
                        <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" onerror="this.style.display='none'">
                    <?php else: ?>
                        <img src="car.png" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                    <?php endif; ?>
                    <article>
                        <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <h3><?php echo htmlspecialchars($auction['categoryName']); ?></h3>
                        <p><?php echo htmlspecialchars($auction['description']); ?></p>
                        <p class="price">Current bid: £<?php echo number_format($auction['highestBid'], 2); ?></p>
                        <a href="auction.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">More >></a>
                    </article>
                </li>
                <br>
                <hr>
                <br>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php
?>
