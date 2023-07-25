<!--This is to view the contacts linked to a specific client-->
<!DOCTYPE html>
<html>
<head>
    <title>Client Details</title>
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

        // Check if the client_id is passed in the URL
        if (isset($_GET['id'])) {
            // Sanitize and validate the client_id to prevent SQL injection
            $specific_client_id = intval($_GET['id']);

            // Query to retrieve client details
            $client_query = "SELECT * FROM clients_information WHERE client_id = :specific_client_id";

            try {
                $client_stmt = $db->prepare($client_query);
                $client_stmt->bindParam(':specific_client_id', $specific_client_id, PDO::PARAM_INT);
                $client_stmt->execute();
                $client = $client_stmt->fetch(PDO::FETCH_ASSOC);

                // Check if the client exists
                if (!$client) {
                    die('Client not found.');
                }

                // Display client details above the table
                echo '<h2>Client Details</h2>';
                echo '<p>Full Name: ' . $client['fullname'] . '</p>';
                echo '<p>Client Code: ' . $client['client_code'] . '</p>';

                // Query to retrieve contacts linked to the specific client
                $contacts_query = "SELECT c.firstname, c.lastname, c.email, c.contact_id 
                                  FROM contacts c
                                  INNER JOIN contact_client_link cl ON c.contact_id = cl.contact_id
                                  WHERE cl.client_id = :specific_client_id
                                  ORDER BY c.lastname ASC, c.firstname ASC";

                try {
                    $stmt = $db->prepare($contacts_query);
                    $stmt->bindParam(':specific_client_id', $specific_client_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($result) === 0) {
                        echo "<p>No contacts found for the specific client.</p>";
                    } else {
                        echo '<h2>Linked Contacts</h2>';
                        echo '<table>';
                        echo '<tr><th>Contact Full Name</th><th>email address</th><th>  </th></tr>';

                        foreach ($result as $row) {
                            $fullname = $row['lastname'] . ' ' . $row['firstname'];
                            $email = $row['email'];
                            $contact_id = $row['contact_id'];

                            echo '<tr>';
                            echo '<td>' . $fullname . '</td>';
                            echo '<td>' . $email . '</td>';
                            echo '<td><a href="unlink_contact.php?client_id=' . $specific_client_id . '&contact_id=' . $contact_id . '" class="action-link">Unlink</a></td>';
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
            echo "Please provide a valid client_id.";
        }

        // Close the database connection
        $db = null;
        ?>
    </div>
</body>
</html>
