<!DOCTYPE html>
<html>
<head>
    <title>Contact Registration</title>
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
    <div class="container">
        <div class="center-container">
            <div class="card">
                <div class="card-body">
                    <?php
                    require_once('contact_process.php');

						if (isset($_POST['create'])) {
							$firstname = $_POST['firstname'];
							$lastname = $_POST['lastname'];
							$email = $_POST['email'];

							$sql = "INSERT INTO contacts (firstname, lastname, email) VALUES(?,?,?)";
							$stmtinsert = $db->prepare($sql);
							$result = $stmtinsert->execute([$firstname, $lastname, $email]);

							if ($result) {
								// Redirect to the contact_table page after successful data insertion
								header("Location: contact_table.php");
								exit(); // Make sure to terminate the script after redirection
							} else {
								echo 'There was an error while saving the data';
							}
						}
                    ?>
                </div>

                <div class="card-body">
                    <form action="create_contact.php" method="post">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <h1>Contact Registration</h1>
                                    <p>Please complete the form below</p>

                                    <label for="firstname">First Name</label>
                                    <input class="form-control" type="text" id="firstname" name="firstname" required>

                                    <label for="lastname">Last Name</label>
                                    <input class="form-control" type="text" id="lastname" name="lastname" required>

                                    <label for="email">Email Address</label>
                                    <input class="form-control" type="email" id="email" name="email" required>
                                    <br>
                                    <input class="btn btn-primary" type="submit" id="sign_up" name="create" value="Register">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(function () {
        $('#sign_up').click(function (event) {
            var firstname = $('#firstname').val().trim();
            var lastname = $('#lastname').val().trim();
            var email = $('#email').val().trim();

            if (firstname === '' || lastname === '' || email === '') {
                // If any field is empty, do not submit the form
                event.preventDefault();

                Swal.fire({
                    title: 'Error',
                    text: 'Please fill in all fields before registering.',
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
                        // If the user confirms, submit the form
                        $('form').submit();
                        Swal.fire(
                            'Contact Created!',
                            'Your details have been saved.',
                            'success'
                        )
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
