<!DOCTYPE html>
<html>
<head>
    <title>Contact List</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* Custom CSS to center the card */
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Contact List</h1>
            <?php
            require_once('contact_process.php');

            // Fetch the contacts ordered by Full Name (Surname followed by Name) in ascending order
            $sql = "SELECT contacts.*, COUNT(contact_client_link.client_id) AS number_of_linked_clients 
                    FROM contacts
                    LEFT JOIN contact_client_link ON contacts.contact_id = contact_client_link.contact_id
                    GROUP BY contacts.contact_id
                    ORDER BY lastname, firstname ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($contacts) > 0) {
                // Display the table if there are contacts
                echo '<table class="table table-bordered">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Full Name</th>';
                echo '<th>Email Address</th>';
                echo '<th class="text-center">No. of linked clients</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($contacts as $contact) {
                    // Concatenate Last Name and First Name to get Full Name
                    $fullName = $contact['lastname'] . ' ' . $contact['firstname'];

                    echo '<tr>';
                    // Display Full Name
                    echo '<td>' . $fullName . '</td>';
                    echo '<td>' . $contact['email'] . '</td>';
                    // Display the number of linked clients for each contact
                    echo '<td class="text-center">' . $contact['number_of_linked_clients'] . '<a href="view_contact_links.php?id=' . $contact['contact_id'] . '"><i class="fas fa-eye"></i></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                // Display "No contact(s) found." if there are no contacts
                echo '<p>No contact(s) found.</p>';
            }
            ?>

            <!-- Linking Form Section (Link one contact with Multiple clients) -->
            <h2>Link Contact to Client</h2>
            <form action="link_client_to_contacts.php" method="post">
                <label for="contact_id">Select Contact:</label>
                <select name="contact_id" id="contact_id">
                    <?php
                    // Fetch contacts again to populate the dropdown for selection
                    $sql = "SELECT * FROM contacts ORDER BY lastname, firstname ASC";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($contacts as $contact) {
                        // Concatenate Last Name and First Name to get Full Name
                        $fullName = $contact['lastname'] . ' ' . $contact['firstname'];
                        echo '<option value="' . $contact['contact_id'] . '">' . $fullName . '</option>';
                    }
                    ?>
                </select><br><br>

                <!-- Client Selector -->
                <label for="client_id">Select Client:</label>
                <select name="client_id" id="client_id">
                    <?php
                    // Fetch clients from the clients table to populate the dropdown for selection
                    $sql = "SELECT * FROM clients_information ORDER BY fullname ASC";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $clients_information = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($clients_information as $client) {
                        echo '<option value="' . $client['client_id'] . '">' . $client['fullname'] . '</option>';
                    }
                    ?>
                </select><br><br>
                <input type="submit" name="link" value="Link Contacts to Client">
            </form>
        </div>
    </div>
</div>
</body>
</html>
