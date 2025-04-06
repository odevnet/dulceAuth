<?php

// Define the project base route
define('BASE_DIR', dirname(__DIR__)); // Return to the root of the project from src/config/

// Define common constants here
define('WEB_PAGE', 'yourwebsite.com'); // without http(s), without www and without ending in /
// some examples: define('WEB_PAGE', 'yourwebsite.com'); or define('WEB_PAGE', 'yourwebsite.com/myFolder');
define('EMAIL_FROM', 'admin@yourwebsite.com');

// Error log
define('LOG_FILE', BASE_DIR . '/logs/log_file.log');

// A little configuration about emails...
define('VERIFICATION_EMAIL_JSON_FILE', BASE_DIR . '/config/verification_email.json'); // json template for verification email. Edit the text as you like
define('VERIFICATION_PAGE_URL', 'verification.php'); // default file where the verification email data is captured

define('FORGOT_PASSWORD_EMAIL_JSON_FILE', BASE_DIR . '/config/forgot_password_email.json'); // json template for forgotten password email. Edit the text as you like
define('FORGOT_PASSWORD_PAGE_URL', 'forgot.php'); // default file where the email data (token and user id) is captured

// Roles. At the moment do not modify anything!!
define('DEFAULT_ROLE', 'User'); // default role
define('DEFAULT_VISIBILITY', 'public'); // default profile visibility

// Accounts
define('VERIFIED', '0'); // 0 = unverified account, requires email validation. 1 = verified
define('MAX_PASSWORD_CHANGES', 3); // password changes allowed per year

// Sessions
define('SESSION_EXPIRATION', 60 * 60); // session lifetime.
//For 1 day: define('SESSION_EXPIRATION', 60 * 60 * 24);
//For 2 days: define('SESSION_EXPIRATION', 60 * 60 * 24 * 2);
//For 7 days: define('SESSION_EXPIRATION', 60 * 60 * 24 * 7);
//For 1 hour: define('SESSION_EXPIRATION', 60 * 60);
