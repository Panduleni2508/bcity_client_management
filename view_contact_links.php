<!--This is to view the linked clients of a specific contact-->
<!DOCTYPE html>
<html>
<head>
    <title>Contact Details</title>
     <style>
         <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-top: 50px; /* Add some margin at the top to center the card container */
        }

        .card {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .card th, .card td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .action-link {
            text-decoration: underline;
            color: blue;
        }
    </style>
    </style>
</head>
<body>
<div class="card">
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

    // Check if the contact_id is passed in the URL
    if (isset($_GET['id'])) {
        // Sanitize and validate the contact_id to prevent SQL injection
        $specific_contact_id = intval($_GET['id']);

        // Query to retrieve contact details
        $contact_query = "SELECT * FROM contacts WHERE contact_id = :specific_contact_id";

        try {
            $contact_stmt = $db->prepare($contact_query);
            $contact_stmt->bindParam(':specific_contact_id', $specific_contact_id, PDO::PARAM_INT);
            $contact_stmt->execute();
            $contact = $contact_stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the contact exists
            if (!$contact) {
                die('Contact not found.');
            }

            // Display contact details above the table
            echo '<h2>Contact Details</h2>';
            echo '<p>Full Name: ' . $contact['lastname'] . ' ' . $contact['firstname'] . '</p>';
            echo '<p>Email: ' . $contact['email'] . '</p>';

            // Query to retrieve linked clients for the specific contact
            $clients_query = "SELECT ci.client_id, ci.fullname, ci.client_code
                              FROM clients_information ci
                              INNER JOIN contact_client_link cl ON ci.client_id = cl.client_id
                              WHERE cl.contact_id = :specific_contact_id
                              ORDER BY ci.fullname ASC";

            try {
                $clients_stmt = $db->prepare($clients_query);
                $clients_stmt->bindParam(':specific_contact_id', $specific_contact_id, PDO::PARAM_INT);
                $clients_stmt->execute();
                $result = $clients_stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) === 0) {
                    echo "<p>No clients linked to this contact.</p>";
                } else {
                    echo '<h2>Linked Clients</h2>';
                    echo '<table>';
                    echo '<tr><th>Full Name</th><th>Client Code</th><th>  </th></tr>';

                    foreach ($result as $row) {
                        $client_fullname = $row['fullname'];
                        $client_code = $row['client_code'];
                        $client_id = $row['client_id'];

                        echo '<tr>';
                        echo '<td>' . $client_fullname . '</td>';
                        echo '<td>' . $client_code . '</td>';
                        echo '<td><a href="unlink_contact.php?contact_id=' . $specific_contact_id . '&client_id=' . $client_id . '" class="action-link">Unlink</a></td>';
                        echo '</tr>';
                    }

                    echo '</table>';
                }
            } catch (PDOException $e) {
                die('Query execution failed: ' . $e->getMessage());
            }
        } catch (PDOException $e) {
            die('Query execution failed: ' . $e->getMessage());
        }
    } else {
        echo "Please provide a valid contact_id.";
    }

    // Close the database connection
    $db = null;
    ?>
</div>
</body>
</html>
