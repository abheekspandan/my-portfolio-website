<?php
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database credentials
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "contact_form_db"; // Your database name
$port = 3308; // MySQL port number

// Email credentials
$to = "2021kucp1005@iiitkota.ac.in"; // Your email address
$subject = "Someone Accessed your Webpage!";

// Create connection using PDO
$dsn = "mysql:host=$servername;port=$port;dbname=$dbname";
$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Initialize variables
$messageSent = false;
$errorMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    // Prepare an SQL statement for inserting the data into the database
    $stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, message) VALUES (:name, :email, :message)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);

    // Execute the prepared statement
    try {
        $stmt->execute();

        // Send notification email to yourself
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '2021kucp1005@iiitkota.ac.in'; // Your Gmail address
            $mail->Password = 'Barman@2002'; // Your app-specific password or Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465; // TCP port to connect to

            // Email to yourself
            $mail->setFrom('2021kucp1005@iiitkota.ac.in', 'Contact Form');
            $mail->addAddress($to);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

            $mail->send();

            // Send confirmation email to user
            $mail->clearAddresses(); // Clear previous recipient
            $mail->addAddress($email); // User's email
            $mail->Subject = "Thank you for contacting me!";
            $mail->Body    = "Dear $name,\n\nThank you for reaching out to me. I have received your message and will get back to you shortly.\n\nBest regards,\nAbheek Spandan";

            $mail->send();
            $messageSent = true;
        } catch (Exception $e) {
            $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }

    // Close the connection
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message {
            font-size: 18px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .error {
            font-size: 18px;
            color: #f44336;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($messageSent): ?>
            <p class="message">Message sent successfully!</p>
            <a href="javascript:history.back()" class="btn">Go Back</a>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
            <a href="javascript:history.back()" class="btn">Go Back</a>
        <?php endif; ?>
    </div>
</body>
</html>
