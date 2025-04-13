<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Auction</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>
            <span class="C">C</span>
            <span class="a">a</span>
            <span class="r">r</span>
            <span class="b">b</span>
            <span class="u">u</span>
            <span class="y">y</span>
        </h1>

        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>

    <nav>
    <ul class="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="category.php">Categories</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <li><a href="adminCategories.php">Manage Categories</a></li>
            <?php else: ?>
                <li><a href="addAuction.php">Add Auction</a></li>
                <li><a href="deleteAuction.php">Delete Auction</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

