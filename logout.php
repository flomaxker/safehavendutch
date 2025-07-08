<?php
ob_start(); // Start output buffering
session_start();
session_destroy();

ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering
header('Location: /login.php');
exit;
