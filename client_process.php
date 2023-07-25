<?php
// client_process.php

$db_user = "root";
$db_pass = "";
$db_name = "bcity";

try {
    $db = new PDO('mysql:host=localhost;dbname=' . $db_name . ';charset=utf8', $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

function generateClientCode($db) {
    $alphabet = range('A', 'Z');

    // Fetch the maximum numeric part from the database
    $stmt = $db->prepare("SELECT MAX(CAST(SUBSTRING(client_code, 4) AS UNSIGNED)) AS max_numeric_part FROM clients_information");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $numericPart = ($row['max_numeric_part'] ?? 0) + 1;

    while (true) {
        $alphaPart = '';
        $len = 3;

        // Ensure the full name is at least three characters long
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        if (strlen($fullname) < 3) {
            // Fill up with alpha characters from A-Z
            $alphaPart = implode('', array_slice($alphabet, 0, $len - strlen($fullname)));
        } else {
            // Take the first 3 characters of the client name as the alpha part
            $alphaPart = strtoupper(substr($fullname, 0, $len));
        }

        $clientCode = $alphaPart . str_pad($numericPart, 3, '0', STR_PAD_LEFT);

        // Check if the generated client code is unique
        $stmt = $db->prepare("SELECT COUNT(*) FROM clients_information WHERE client_code = ?");
        $stmt->execute([$clientCode]);
        $count = $stmt->fetchColumn();

        if ($count === 0) {
            // If the client code is unique, return it
            return $clientCode;
        }

        // Increment numeric part if not unique
        $numericPart++;
    }
}

if (isset($_POST['create'])) {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';

    if (!empty($fullname)) {
        // Validate and sanitize the full name input
        // Use parameterized queries to prevent SQL injection
        $fullname = htmlspecialchars($fullname);
        $clientCode = generateClientCode($db);

        try {
            // Attempt to insert into the database using a transaction
            $db->beginTransaction();
            $sql = "INSERT INTO clients_information (fullname, client_code) VALUES (?, ?)";
            $stmtinsert = $db->prepare($sql);
            $stmtinsert->execute([$fullname, $clientCode]);
            $db->commit();
            echo 'Successfully Created';
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $db->rollBack();
            echo 'There was an error while saving the data';
        }
    } else {
        echo 'Please enter your full name before registering.';
    }
}
?>
