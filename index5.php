<?php

session_start();
require_once 'auth.php';

// Check if user is logged in, gibberish stuff
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$host = 'localhost'; 
$dbname = 'drinks'; 
$user = 'jacob'; 
$pass = 'jacob';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Handle book search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT drink_id, brand, cup_size, publisher FROM data WHERE cup_size LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['brand']) && isset($_POST['cup_size']) && isset($_POST['publisher'])) {
        // Insert new entry
        $brand = htmlspecialchars($_POST['brand']);
        $cup_size = htmlspecialchars($_POST['cup_size']);
        $publisher = htmlspecialchars($_POST['publisher']);
        
        $insert_sql = 'INSERT INTO data (brand, cup_size, publisher) VALUES (:brand, :cup_size, :publisher)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['brand' => $brand, 'cup_size' => $cup_size, 'publisher' => $publisher]);
    } elseif (isset($_POST['delete_drink_id'])) {
        // Delete an entry
        $delete_drink_id = (int) $_POST['delete_drink_id'];
        
        $delete_sql = 'DELETE FROM data WHERE drink_id = :drink_id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['drink_id' => $delete_drink_id]);
    }
}

// Get all data for main table
$sql = 'SELECT drink_id, brand, cup_size, publisher FROM data';
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Betty's Book Banning and Brdrink_idge Building</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Betty's Book Banning and Brdrink_idge Building</h1>
        <p class="hero-subtitle">"Because nothing brings a community together like collectively decdrink_iding what others shouldn't read!"</p>
        
        <!-- Search moved to hero section -->
        <div class="hero-search">
            <h2>Search for a Book to Ban</h2>
            <form action="" method="GET" class="search-form">
                <label for="search">Search by cup_size:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search">
            </form>
            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>drink_id</th>
                                    <th>brand</th>
                                    <th>cup_size</th>
                                    <th>Publisher</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['drink_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cup_size']); ?></td>
                                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                                    <td>
                                        <form action="index5.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_drink_id" value="<?php echo $row['drink_id']; ?>">
                                            <input type="submit" value="Ban!">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No data found matching your search.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table section with container -->
    <div class="table-container">
        <h2>All data in Database</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>drink_id</th>
                    <th>brand</th>
                    <th>cup_size</th>
                    <th>Publisher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['drink_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td><?php echo htmlspecialchars($row['cup_size']); ?></td>
                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hdrink_idden" name="delete_drink_id" value="<?php echo $row['drink_id']; ?>">
                            <input type="submit" value="Ban!">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
        <h2>Condemn a Book Today</h2>
        <form action="index5.php" method="post">
            <label for="brand">brand:</label>
            <input type="text" id="brand" name="brand" required>
            <br><br>
            <label for="cup_size">cup_size:</label>
            <input type="text" id="cup_size" name="cup_size" required>
            <br><br>
            <label for="publisher">Publisher:</label>
            <input type="float" id="publisher" name="publisher" required>
            <br><br>
            <input type="submit" value="Condemn Book">
        </form>
    </div>
</body>
</html>