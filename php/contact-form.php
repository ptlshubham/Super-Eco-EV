<?php
namespace PortoContactForm;

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));
header('Content-type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'php-mailer/src/PHPMailer.php';
require 'php-mailer/src/SMTP.php';
require 'php-mailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'Support@superecoev.in';
    $mail->Password   = 'Support3303#';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Get and sanitize form data
    $form_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $contact_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contact_phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : '';
    $contact_message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    // Basic validation
    if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }
    if (empty($form_name) || empty($contact_message)) {
        throw new Exception('Name and message are required.');
    }

    // Anti-spam logic
    if (
        preg_match('/http|www|\.com|\.org|\.net/i', $contact_message) || 
        strtoupper($contact_message) === $contact_message || 
        strlen($contact_message) > 500
    ) {
        throw new Exception('Invalid message content.');
    }

    // reCAPTCHA Validation
    $recaptcha_secret = '6LdKHFErAAAAANmG3SLcGCA8WJ_rrYWi7rqEMxnL'; // Replace if needed
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $recaptcha = json_decode($verify);
    if (!$recaptcha->success) {
        throw new Exception('CAPTCHA verification failed.');
    }

    // Send Email
    $mail->setFrom('Support@superecoev.in', 'Super Eco');
    $mail->addAddress($contact_email, $form_name);
    $mail->addReplyTo('Support@superecoev.in', 'Super Eco');
    $mail->addAddress('Support@superecoev.in');

    $mail->isHTML(true);
    $mail->Subject = 'Thank You for Contacting Super Eco';

    $mail->Body = '
    <html>
  <body style="font-family: Open Sans, Helvetica, Arial, sans-serif; background-color: #F2F2F2;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background: #FFFFFF; border-radius: 10px;">
        <img src="https://superecoev.in/images/Spuer-Eco.png" alt="Super Eco" 
             style="display: block; margin: 0 auto 20px auto; max-width: 80px;">
        <h2 style="color: #4F4F4F;">Thank You for Reaching Out!</h2>
        <p style="color: #4F4F4F;">Dear ' . htmlspecialchars($form_name) . ',</p>
        <p style="color: #4F4F4F;">We’ve received your message and will get back to you soon.</p>
        <ul style="color: #4F4F4F;">
            <li><strong>Name:</strong> ' . htmlspecialchars($form_name) . '</li>
            <li><strong>Email:</strong> ' . htmlspecialchars($contact_email) . '</li>
            <li><strong>Phone:</strong> ' . htmlspecialchars($contact_phone) . '</li>
            <li><strong>Message:</strong> ' . htmlspecialchars($contact_message) . '</li>
        </ul>
        <p style="color: #4F4F4F;">Best regards,<br>Super Eco Team</p>
    </div>
    <div style="text-align: center; font-size: 12px; color: #4F4F4F; padding: 10px;">
        © 2025 Super Eco. All rights reserved.
    </div>
</body>

    </html>';

    $mail->send();

    // $username = "root";
    // $servername = "localhost";
    // $database = "supereco";
    // $password = "";

    // Save to database
    $servername = "127.0.0.1:3306";
    $username = "u768511311_superco";
    $password = "SuperEco@1234";
    $database = "u768511311_superco";

    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

   $sql = "INSERT INTO `contact` (`name`, `email`, `phone`, `message`) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $form_name, $contact_email, $contact_phone, $contact_message);

if (!mysqli_stmt_execute($stmt)) {
    throw new Exception("Database error: " . mysqli_error($conn));
}


    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
