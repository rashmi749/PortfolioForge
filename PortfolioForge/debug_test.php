<?php
// debug_test.php - Temporary Diagnostic File
// DELETE THIS FILE AFTER DEBUGGING

echo "<h2>PortfolioForge Debug Test</h2>";
echo "<pre>";

// Test 1: PHP Version
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Test 2: PDO MySQL extension
echo "PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? "YES ✓" : "NO ✗ - Enable pdo_mysql in php.ini") . "\n\n";

// Test 3: Database Connection
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=portfolio_forge;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Database Connection: OK ✓\n\n";

    // Test 4: List tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables Found (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo "  - $t\n";
    }
    echo "\n";

    // Test 5: Check users table specifically
    if (in_array('users', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "Users Table: OK ✓ ($count records)\n\n";

        // Test 6: Check admin seeded
        $admin = $pdo->query("SELECT username, role FROM users WHERE role = 'admin'")->fetch();
        if ($admin) {
            echo "Admin Account: FOUND ✓ (username: {$admin['username']})\n\n";
        } else {
            echo "Admin Account: NOT FOUND - Seeding now...\n";
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@portfolioforge.com', '$hash', 'admin') ON DUPLICATE KEY UPDATE username=username");
            echo "Admin Account: SEEDED ✓\n\n";
        }
    } else {
        echo "users TABLE MISSING! Run schema.sql first.\n\n";

        // Auto-run schema
        echo "Attempting to run schema.sql automatically...\n";
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($sql);
        echo "Schema imported! Refresh this page to verify.\n\n";
    }

    // Test 7: Check portfolios table 
    if (in_array('portfolios', $tables)) {
        echo "Portfolios Table: OK ✓\n";
    }

    // Test 8: Uploads folder
    $upload_dir = __DIR__ . '/uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
        echo "\nuploads/ folder: CREATED ✓\n";
    } else {
        echo "\nuploads/ folder: EXISTS ✓ (writable: " . (is_writable($upload_dir) ? "YES ✓" : "NO ✗") . ")\n";
    }

} catch (PDOException $e) {
    echo "Database Connection: FAILED ✗\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "SOLUTIONS:\n";
    echo "1. Make sure MySQL is started in XAMPP Control Panel\n";
    echo "2. Make sure 'portfolio_forge' database was created in phpMyAdmin\n";
    echo "3. Import schema.sql from phpMyAdmin > Import tab\n";
}

echo "\n--- Test Complete ---";
echo "</pre>";

echo "<br><br><a href='/PortfolioForge/register.php'>Go to Register Page</a> | ";
echo "<a href='/PortfolioForge/login.php'>Go to Login Page</a>";
?>
