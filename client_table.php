<!DOCTYPE html>

<!--- This page is for displaying the registered cliennts, client codes and number or linked contacts-->
<html>
<head>
    <title>List of Clients</title>
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
    <div class="center-container">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center">List of Clients</h1>

                    <?php
                    require_once('client_process.php');

                    // Fetch client data from the database, ordered by Name ascending
                    $sql = "SELECT fullname, client_code, clients_information.client_id, COUNT(contact_client_link.contact_id) as linked_contacts
					FROM clients_information
					 LEFT JOIN contact_client_link ON clients_information.client_id = contact_client_link.client_id
					GROUP BY fullname, client_code, clients_information.client_id 
					ORDER BY fullname ASC";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $clients_information = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($clients_information) > 0) {
                        // If there are clients to display, create the table
                        echo '<table class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th scope="col">Name</th>';
                        echo '<th scope="col">Client Code</th>';
                        echo '<th scope="col">No. of Linked Contacts</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        foreach ($clients_information as $client) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($client['fullname']) . '</td>';
                            echo '<td>' . htmlspecialchars($client['client_code']) . '</td>';
                          echo '<td style="text-align: center;">' . $client['linked_contacts'] . '  <a href="view_client_links.php?id=' . $client['client_id'] . '"><i class="fas fa-eye"></i></a></td>';

                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        // If there are no clients to display, show a message
                        echo '<p class="text-center">No client(s) found.</p>';
                    }
                    ?>
					
					 <!-- Linking Form Section (Link one Client with Multiple Cotacts) -->
            <h2>Link Client to Contact</h2>
            <form action="link_client_to_contacts.php" method="post">
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
				
                <input type="submit" name="link" value="Link Clients to Contact">
            </form>
					
                </div>
            </div>
			
        </div>
    </div>
</body>
</html>
