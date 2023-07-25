<?php
// Ensure that this script is accessed through a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['link'])) {
    require_once('contact_process.php');

    // Get the selected contact_id and client_id from the form submission
    $contact_id = $_POST['contact_id'];
    $client_id = $_POST['client_id'];

    // Insert the linking information into the contact_client_link table
    $sql = "INSERT INTO contact_client_link (contact_id, client_id) VALUES (?, ?)";
    $stmt = $db->prepare($sql);

    // Perform the database query and handle potential errors
    try {
        $stmt->execute([$contact_id, $client_id]);

        // Redirect to the current page after successful linking
        echo "<script>window.location.replace('{$_SERVER['PHP_SELF']}');</script>";
        exit(); // Make sure to terminate the script after redirection
    } catch (PDOException $e) {
        echo 'Error linking contact to client: ' . $e->getMessage();
    }
}
?>
