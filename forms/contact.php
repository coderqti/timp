<?php
header('Content-Type: application/json'); // Set the content type to JSON for AJAX response

// Define variables and set to empty values
$nameErr = $emailErr = $subjectErr = "";
$name = $email = $message = $subject = "";

// Form validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $valid = true;

  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
    $valid = false;
  } else {
    $name = test_input($_POST["name"]);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
      $nameErr = "Only letters and white space allowed";
      $valid = false;
    }
  }

  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
    $valid = false;
  } else {
    $email = test_input($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
      $valid = false;
    }
  }

  if (empty($_POST["subject"])) {
    $subjectErr = "Subject is required";
    $valid = false;
  } else {
    $subject = test_input($_POST["subject"]);
  }

  if (empty($_POST["message"])) {
    $messageErr = "Message is required";
    $valid = false;
  } else {
    $message = test_input($_POST["message"]);
  }

  // If all validations pass, send the email
  if ($valid) {
    $receiving_email_address = 't.mwangim02@gmail.com';

    if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
      include($php_email_form);
    } else {
      die(json_encode(['status' => 'error', 'message' => 'Unable to load the "PHP Email Form" Library!']));
    }

    $contact = new PHP_Email_Form;
    $contact->ajax = true;

    $contact->to = $receiving_email_address;
    $contact->from_name = $name;
    $contact->from_email = $email;
    $contact->subject = $subject;

    // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
    /*
    $contact->smtp = array(
      'host' => 'example.com',
      'username' => 'example',
      'password' => 'pass',
      'port' => '587'
    );
    */

    $contact->add_message($name, 'From');
    $contact->add_message($email, 'Email');
    $contact->add_message($message, 'Message', 10);

    if ($contact->send()) {
      echo json_encode(['status' => 'success', 'message' => 'Your message has been sent. Thank you!']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'There was an error sending your message.']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Validation error.']);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
