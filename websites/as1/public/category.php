<?php
session_start();

try {
    $pdo = new PDO("mysql:host=db;dbname=assignment1", "user", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM category ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($categoryId) {
    $stmt = $pdo->prepare("SELECT name FROM category WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        die("Category not found.");
    }

    $stmt = $pdo->prepare("SELECT * FROM auction WHERE categoryId = ? ORDER BY endDate ASC");
    $stmt->execute([$categoryId]);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';
?>

<main>
    <h1>Browse Categories</h1>
    
    <nav class="category-nav">
        <?php foreach ($categories as $cat): ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>" 
               class="category-link <?php echo ($cat['id'] == $categoryId) ? 'active' : ''; ?>">
               <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ($categoryId): ?>
        <h1><?php echo htmlspecialchars($category['name']); ?> Auctions</h1>

        <?php if (empty($auctions)): ?>
            <p>No auctions found in this category.</p>
        <?php else: ?>
            <div class="car-listings">
                <?php foreach ($auctions as $auction): ?>
                    <div class="car-item">
                        <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <p><?php echo htmlspecialchars($auction['description']); ?></p>
                        <p>Ends: <?php echo htmlspecialchars($auction['endDate']); ?></p>
                        <a class="more auctionLink" href="auction.php?id=<?php echo $auction['id']; ?>">More</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>Select a category to view auctions.</p>
    <?php endif; ?>
</main>

<style>
    .category-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 8px;
    }

    .category-link {
        text-decoration: none;
        padding: 8px 15px;
        background: #ddd;
        border-radius: 5px;
        color: black;
        transition: 0.3s;
    }

    .category-link:hover, .category-link.active {
        background: #555;
        color: white;
    }

    .car-listings {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .car-item {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .car-item h2 {
        margin: 0 0 10px;
    }

    .more {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 10px;
        background: #333;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .more:hover {
        background: #555;
    }
</style>
