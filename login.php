<?php
// Database connection parameters
$hostname = "localhost";
$username = "root";
$password = "";
$database = "phs_enrollment";

// Establish a connection to the database
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}

// Check if the email and password are provided in the POST request
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Sanitize user input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if the email exists in the "accounts" table
    $query = "SELECT * FROM accounts WHERE Email = '$email'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 0) {
        // Email does not exist in the database
        header("Location: ndex.php?message=Email does not exist");
        exit();
    } else {
        // Email exists, check the password
        $user = mysqli_fetch_assoc($result);
        $hashedPassword = $user['Password']; // Assuming you store hashed passwords in the database

        if (password_verify($password, $hashedPassword)) {
            if ($user['verify'] == 0) {
                // Account not yet verified
                header("Location: ndex.php?message=Account not yet verified, please verify to log in");
                exit();
            } else {
                // Login successful, redirect based on the email domain
                $emailDomain = substr(strrchr($email, "@"), 1); // Extract the email domain

                if ($emailDomain === 'outlook.com') {
                    // Redirect to the admin dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($emailDomain === 'gmail.com') {
                    // Redirect to the user's dashboard
                    header("Location: dashboard.html");
                    exit();
                }
            }
        } else {
            // Incorrect password
            header("Location: ndex.php?message=Incorrect email or password");
            exit();
        }
    }
} else {
    // Handle the case where email and password are not set in the POST request
    echo "Email and password are required";
    header("Location: ndex.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
