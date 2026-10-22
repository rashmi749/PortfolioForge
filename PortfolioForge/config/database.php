<?php
/**
 * PortfolioForge Database Configuration
 * Uses PDO for secure database connection with prepared statements.
 */

$db_host = 'localhost';
$db_name = 'portfolio_forge';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // Lightweight auto-migration: ensure the 'layout' column exists on portfolios
    // (adds portfolio template/layout selection without requiring a manual SQL update)
    try {
        $col_check = $pdo->query("SHOW COLUMNS FROM portfolios LIKE 'layout'");
        if ($col_check && $col_check->rowCount() === 0) {
            $pdo->exec("ALTER TABLE portfolios ADD COLUMN layout VARCHAR(50) NOT NULL DEFAULT 'minimal' AFTER theme");
        }
    } catch (PDOException $e) {
        // Table may not exist yet on a fresh install; schema.sql will define it directly.
    }
} catch (PDOException $e) {
    // In production, you would log this and show a friendly message.
    die("Database Connection Failed: " . $e->getMessage());
}
