<?php
ob_start(); // Start output buffering
session_start(); // Ensure session is started before destroying
session_unset();
session_destroy();

ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering
header('Location: /login.php'); // Redirect to the main login page
exit();
