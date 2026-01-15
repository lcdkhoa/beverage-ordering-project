<?php
/**
 * Database Configuration
 * Kết nối tới SQL Server (MSSQL)
 */

// Database credentials
define('DB_HOST', 'localhost\\SQLEXPRESS'); // Update instance/name theo môi trường thực tế
define('DB_USER', 'sa');
define('DB_PASS', 'yourStrong(!)Password');
define('DB_NAME', 'meowtea_schema');

/**
 * Get database connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // DSN cho SQL Server (PDO_SQLSRV)
            $dsn = "sqlsrv:Server=" . DB_HOST . ";Database=" . DB_NAME . ";TrustServerCertificate=true";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Không thể kết nối tới database. Vui lòng thử lại sau.");
        }
    }
    
    return $pdo;
}

/**
 * Test database connection
 */
function testDBConnection() {
    try {
        $pdo = getDBConnection();
        return $pdo !== null;
    } catch (Exception $e) {
        return false;
    }
}
?>
