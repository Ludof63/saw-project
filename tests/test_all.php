<?php

require 'test_login.php';
require 'test_register.php';
require 'test_show.php';
require 'test_update.php';
require 'utils.php';

/****************************************/
/* replace $baseurl with your directory */
/****************************************/

// local URL
// $baseurl =  'http://localhost/....';

// remote URL
if (count($argv) != 2) {
    echo "Missing argument baseurl";
    exit(1);
}
$baseurl =  $argv[1];


echo "[+] Testing Registration - Login - Show Profile\n";

echo "[*] Generating random user\n";

echo "---\n";
$email = generate_random_email();
$pass = generate_random_password();
$first_name = generate_random_name();
$last_name = generate_random_name();
echo "Email: $email\n";
echo "Pass: $pass\n";
echo "First name: $first_name\n";
echo "Last name: $last_name\n";
echo "---\n";

echo "[-] Calling registration.php\n";

register($email, $pass, $first_name, $last_name, $baseurl);

echo "[-] Calling login.php\n";
login($email, $pass, $baseurl);


echo "[-] Calling show_profile.php\n";

if (check_correct_user($email, $first_name, $last_name, show_logged_user($baseurl))) {
    echo "[*] Success!\n";
} else {
    echo "[*] Failed\n";
    exit(1);
}

echo "------------------------\n";

echo "[+] Testing Update - Show Profile\n";

echo "[*] Generating new random user\n";
$first_name = generate_random_name();
$last_name = generate_random_name();

echo "---\n";
echo "Email: $email\n";
echo "First name: $first_name\n";
echo "Last name: $last_name\n";
echo "---\n";

echo "[-] Calling update_profile.php\n";
update($email, $first_name, $last_name, $baseurl);

echo "[-] Calling show_profile.php\n";

if (check_correct_user($email, $first_name, $last_name, show_logged_user($baseurl))) {
    echo "[*] Success!\n";
} else {
    echo "[*] Failed\n";
    exit(1);
}


echo "------------------------\n";
