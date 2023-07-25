<!DOCTYPE html>

<!--- This page is for registering cliennts-->
<html>
<head>
    <title>Client Registration</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="card">
            <div class="card-body">
                <?php
                require_once('client_process.php');

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['create'])) {
                        $fullname = $_POST['fullname'];

                        if (!empty($fullname)) {
                            // Generate the client code
                            $clientCode = generateClientCode($db);
                            echo "Client Code: " . $clientCode . "<br>"; // Display the client code

                            // Redirect to client_table.php after saving the data
                            header('Location: client_table.php');
                            exit;
                        }
                    }
                }
                ?>
            </div>

            <div class="card-body">
                <form action="clients_registration.php" method="post">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h1>Client Registration</h1>
                                <p>Please complete the form below</p>

                                <label for="fullname">Full Name</label>
                                <input class="form-control" type="text" id="fullname" name="fullname" required>
                                <br>
                                <input class="btn btn-primary" type="submit" id="sign_up" name="create" value="Register">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(function () {
        $('#sign_up').click(function (event) {
            var fullname = $('#fullname').val().trim();
            if (fullname === '') {
                // If fullname is empty, do not submit the form
                event.preventDefault();

                Swal.fire({
                    title: 'Error',
                    text: 'Please enter your full name before registering.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            } else {
                // Show the confirmation dialog using Swal library
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Have you entered the correct details?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save details!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If the user confirms, proceed with form submission
                        // The form will be submitted to the current page (clients_registration.php)
                    } else {
                        // If the user cancels the submission, prevent form submission
                        event.preventDefault();
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
