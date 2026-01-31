<?php
// Password generation utility for TechnoKeeper
// This script generates a secure password hash for the admin user

require_once 'db.php';

// Default password for admin user
$defaultPassword = 'admin123';
$username = 'john.doe';

// Generate secure password hash
$passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

echo "Generated password hash for user '{$username}':\n";
echo "Password: {$defaultPassword}\n";
echo "Hash: {$passwordHash}\n\n";

// Update the database with the new hash
$updateStmt = mysqli_prepare($connect, "
    UPDATE users 
    SET password_hash = ? 
    WHERE username = ?
");

if ($updateStmt) {
    mysqli_stmt_bind_param($updateStmt, "ss", $passwordHash, $username);
    
    if (mysqli_stmt_execute($updateStmt)) {
        echo "Password hash updated successfully in database!\n";
        echo "You can now login with:\n";
        echo "Username: {$username}\n";
        echo "Password: {$defaultPassword}\n";
    } else {
        echo "Error updating password: " . mysqli_error($connect) . "\n";
    }
    
    mysqli_stmt_close($updateStmt);
} else {
    echo "Error preparing statement: " . mysqli_error($connect) . "\n";
}

mysqli_close($connect);
?>
