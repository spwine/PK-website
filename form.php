<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Your form data retrieval code remains the same
  $firstName = $_POST["firstName"];
  $lastName = $_POST["lastName"];
  $email = $_POST["email"];
  $telephone = $_POST["phone"];
  $topic = $_POST["subject"];
  $message = $_POST["message"];

  $recipientEmails = array(
    // "info@pkintercorp.com",
    // "kanpol_ken@pmvalve.com"
    "pongsakorn.wine@hotmail.com"
  );

  // Validate input data to prevent abuse
  if (empty($firstName) || empty($lastName) || empty($email) || empty($telephone) || empty($topic) || empty($message)) {
    echo "Please fill in all required fields.";
    exit;
  }

  // Additional validation and sanitization can be added as needed

  // reCAPTCHA verification
  $recaptchaSecretKey = '6LdPBRYpAAAAAC-V0IqJK75sOFhQDdStf7QwHNsn'; // Replace with your reCAPTCHA Secret Key
  $recaptchaResponse = $_POST['g-recaptcha-response'];

  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = [
    'secret' => $recaptchaSecretKey,
    'response' => $recaptchaResponse,
  ];

  $options = [
    'http' => [
      'header' => "Content-type: application/x-www-form-urlencoded\r\n",
      'method' => 'POST',
      'content' => http_build_query($data),
    ],
  ];

  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  $responseKeys = json_decode($result, true);

  // Check if the reCAPTCHA response is valid
  if (!$responseKeys["success"]) {
    echo "reCAPTCHA verification failed.";
    exit;
  }

  // Compose the email message
  $headers = "From: support_it1@pmvalve.com";
  $subject = "Contact Form Submission from PK Intercorp website";
  $message = "First name: $firstName\nLast name: $lastName\nEmail: $email\nTelephone: $telephone\nSubject: $topic\nMessage: $message";

  // Initialize a variable to track if the email was sent successfully
  $emailSent = true;

  // Send the email to each recipient
  foreach ($recipientEmails as $recipientEmail) {
    if (!mail($recipientEmail, $subject, $message, $headers)) {
      // If any email fails to send, set $emailSent to false
      $emailSent = false;
      break; // Stop sending emails on the first failure (optional)
    }
  }

  if ($emailSent) {
    echo "Email sent successfully.";
  } else {
    echo "Failed to send the email.";
  }
}
