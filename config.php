<?php
// config.php

// Define the base URL for the site for consistent linking
define('BASE_URL', '/ringzz/');
session_start();

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','ringzz');

/* RAZORPAY KEYS - use test keys for development */
define('RAZORPAY_KEY_ID', 'rzp_test_FjIWY7OUwRhbVn');
define('RAZORPAY_KEY_SECRET', 'pxQaQUudekWwLgos0xOd2wUB');

/* For production: replace with live keys and use HTTPS */
