<?php
$db_user = "root";
$db_pass = "";
$db_name = "bcity";

try {
    $db = new PDO('mysql:host=localhost;dbname=' . $db_name . ';charset=utf8', $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Check if client_id and contact_id are provided in the URL
if (isset($_GET['client_id']) && isset($_GET['contact_id'])) {
    // Sanitize and validate the client_id and contact_id to prevent SQL injection
    $client_id = intval($_GET['client_id']);
    $contact_id = intval($_GET['contact_id']);

    // Query to unlink the contact from the client
    $unlink_query = "DELETE FROM contact_client_link WHERE client_id = :client_id AND contact_id = :contact_id";

    try {
        $unlink_stmt = $db->prepare($unlink_query);
        $unlink_stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $unlink_stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
        $unlink_stmt->execute();

        // Check if any rows were affected (i.e., if the unlinking was successful)
        if ($unlink_stmt->rowCount() > 0) {
            // Redirect back to the view_client_links.php page with the specific client_id
            header("Location: view_client_links.php?id=" . $client_id);
            exit();
        } else {
            die('Failed to unlink the contact from the client.');
        }
    } catch (PDOException $e) {
        die('Query execution failed: ' . $e->getMessage());
    }
} else {
    die('Missing client_id or contact_id.');
}

// Close the database connection
$db = null;
?>
