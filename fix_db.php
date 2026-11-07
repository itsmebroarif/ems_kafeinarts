<?php
// Direct database fix for the voucher_id foreign key
// This script alters the column type and adds the foreign key

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "db_ems_kafeinarts";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Fixing voucher_id foreign key in registrants table...\n";

// Step 1: Change the column type from unsignedBigInteger to char(36) for UUID
// First, let's check the current type
$colSql = "DESCRIBE registrants voucher_id";
$colResult = $conn->query($colSql);
$colRow = $colResult->fetch_assoc();
echo "  - Current type: " . $colRow['Type'] . "\n";

// Step 2: Try to change the column type to char(36) and keep it nullable
// MySQL ALTER TABLE to change type
$changeSql = "ALTER TABLE registrants MODIFY COLUMN voucher_id CHAR(36) NULL";
$changeResult = $conn->query($changeSql);
if ($changeResult === TRUE) {
    echo "  - Changed column type to CHAR(36) NULL\n";
} else {
    echo "  - Could not change column type: " . $conn->error . "\n";
}

// Step 3: Now try to add the foreign key constraint
$addSql = "ALTER TABLE registrants ADD CONSTRAINT registrants_voucher_id_foreign 
           FOREIGN KEY (voucher_id) REFERENCES event_vouchers(id) ON DELETE SET NULL";
$addResult = $conn->query($addSql);
if ($addResult === TRUE) {
    echo "  - Successfully added foreign key constraint\n";
} else {
    echo "  - Failed to add foreign key: " . $conn->error . "\n";
}

// Step 4: Verify
$verifySql = "SHOW INDEX FROM registrants WHERE Key_name = 'registrants_voucher_id_foreign'";
$verifyResult = $conn->query($verifySql);
if ($verifyResult->num_rows > 0) {
    $verifyRow = $verifyResult->fetch_assoc();
    echo "  - Verified: Foreign key exists\n";
    echo "  - Constraint: " . $verifyRow['Key_name'] . "\n";
    echo "  - Referenced table: " . $verifyRow['Table'] . "\n";
} else {
    echo "  - Verification: Foreign key not found\n";
}

$conn->close();
echo "\nDone!\n";
?>