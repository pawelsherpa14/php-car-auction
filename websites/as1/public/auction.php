<?php
session_start();
try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$auctionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.description, a.endDate, a.image, a.userId AS auctionUserId, c.name AS categoryName, u.name AS userName
    FROM auction a
    JOIN category c ON a.categoryId = c.id
    JOIN user u ON a.userId = u.id
    WHERE a.id = ?
");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found.");
}

$bidStmt = $pdo->prepare("
    SELECT b.amount AS highestBid, u.name AS bidderName
    FROM bid b
    JOIN user u ON b.userId = u.id
    WHERE b.auctionId = ?
    AND b.amount = (
        SELECT MAX(amount)
        FROM bid
        WHERE auctionId = ?
    )
    LIMIT 1
");
$bidStmt->execute([$auctionId, $auctionId]);
$highestBid = $bidStmt->fetch(PDO::FETCH_ASSOC);


$bidHistoryStmt = $pdo->prepare("
    SELECT b.amount, u.name AS bidderName
    FROM bid b
    JOIN user u ON b.userId = u.id
    WHERE b.auctionId = ?
    ORDER BY b.amount DESC
");
$bidHistoryStmt->execute([$auctionId]);
$bidHistory = $bidHistoryStmt->fetchAll(PDO::FETCH_ASSOC);


$reviewStmt = $pdo->prepare("
    SELECT r.reviewText, r.datePosted, u.name AS reviewerName, u.email AS reviewerEmail
    FROM review r
    JOIN user u ON r.userId = u.id
    WHERE r.reviewedUserId = ?
");
$reviewStmt->execute([$auction['auctionUserId']]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

function getRemainingTime($endDate) {
    $now = new DateTime();
    $end = new DateTime($endDate);
    if ($end < $now) {
        return "Auction ended";
    }
    $interval = $now->diff($end);
    return $interval->format('%d days, %h hours, %i minutes');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid']) && isset($_SESSION['user_id'])) {
    $bidAmount = filter_input(INPUT_POST, 'bid', FILTER_VALIDATE_FLOAT);
    $userId = $_SESSION['user_id'];

    if ($bidAmount === false || $bidAmount <= 0) {
        $bidError = "Please enter a valid bid amount.";
    } elseif ($highestBid && $bidAmount <= $highestBid['highestBid']) {
        $bidError = "Your bid must be higher than the current highest bid (£" . number_format($highestBid['highestBid'], 2) . ").";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO bid (auctionId, userId, amount) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$auctionId, $userId, $bidAmount]);
            $bidSuccess = "Bid placed successfully!";
            header("Location: auction.php?id=$auctionId");
            exit;
        } catch (PDOException $e) {
            $bidError = "Error placing bid: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewText']) && isset($_SESSION['user_id'])) {
    $reviewText = $_POST['reviewText'];
    $userId = $_SESSION['user_id'];
    $reviewedUserId = $auction['auctionUserId'];

    if (empty($reviewText)) {
        $reviewError = "Review cannot be empty.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO review (reviewText, userId, reviewedUserId, auctionId) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$reviewText, $userId, $reviewedUserId, $auctionId]);
            $reviewSuccess = "Review added successfully!";
            header("Location: auction.php?id=$auctionId");
            exit;
        } catch (PDOException $e) {
            $reviewError = "Error adding review: " . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<main>
    <article class="car">
        <img src="<?php echo !empty($auction['image']) ? htmlspecialchars($auction['image']) : 'car.png'; ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" style="width:100%;height:80vh;">
        <section class="details">
            <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
            <h3><?php echo htmlspecialchars($auction['categoryName']); ?></h3>
            <p>Auction created by <a href="#"><?php echo htmlspecialchars($auction['userName']); ?></a></p>
            <p class="price">Current bid: £<?php echo $highestBid ? number_format($highestBid['highestBid'], 2) : '0.00'; ?></p>
            <time>Time left: <?php echo getRemainingTime($auction['endDate']); ?></time>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="" method="POST" class="bid">
                    <input type="text" name="bid" placeholder="Enter bid amount" required>
                    <input type="submit" value="Place bid">
                </form>
                <?php if (isset($bidSuccess)): ?>
                    <p style="color: green;"><?php echo $bidSuccess; ?></p>
                <?php elseif (isset($bidError)): ?>
                    <p style="color: red;"><?php echo $bidError; ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to place a bid.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $auction['auctionUserId']): ?>
                <p><a href="editAuction.php?id=<?php echo $auction['id']; ?>">Edit Auction</a></p>
            <?php endif; ?>
        </section>


        <section class="description">
            <p><?php echo htmlspecialchars($auction['description']); ?></p><br><br><br>
        </section>

        <hr>
        <br>
        <section class="bid-history">
            <h3>Bid History</h3>
            <?php if (empty($bidHistory)): ?>
                <p>No bids yet.</p>
            <?php else: ?>
                <ul style="color:green;list-style-type:number;">
                    <?php foreach ($bidHistory as $bid): ?>
                        <li><?php echo htmlspecialchars($bid['bidderName']); ?> bid £<?php echo number_format($bid['amount'], 2); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <br>
        <hr>        
        <br>

        <section class="reviews">
            <h3>Reviews of <?php echo htmlspecialchars($auction['userName']); ?></h3>
            <?php if (empty($reviews)): ?>
                <p>No reviews yet.</p>
            <?php else: ?>
                <ul style="list-style-type:disc;">
                    <?php foreach ($reviews as $review): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($review['reviewerName']); ?></strong> 
                            (<?php echo htmlspecialchars($review['reviewerEmail']); ?>) 
                            <p>-><?php echo htmlspecialchars($review['reviewText']); ?></p>
                            <small><em>---<?php echo htmlspecialchars($review['datePosted']); ?></em></small>
                        </li>
                        <br>
                        <hr>
                        <br>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="">
                    <strong><label>Add your review</label></strong><br>
                    <textarea name="reviewText" required ></textarea><br>
                    <input type="submit" value="Add Review"><br><br>
                </form>
                <?php if (isset($reviewSuccess)): ?>
                    <p style="color: green;"><?php echo $reviewSuccess; ?></p>
                <?php elseif (isset($reviewError)): ?>
                    <p style="color: red;"><?php echo $reviewError; ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to leave a review.</p>
            <?php endif; ?>
        </section>
    </article>

    <p><a href="index.php">Back to Auctions</a></p>
</main>