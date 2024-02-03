<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$validators = [
    "email" => "validate_email",
    "pass" => "validate_password",
    "remember" => "validate_bool"
];
$values = validate($validators, true, ["remember" => "false"]);

$db = new DB();
$user = get_user($db, get($values, "email"));

if ($user === null || !password_verify(get($values, "pass"), $user[1])) {
    json_response(false);
}

$session = Session::from_id($db, $user[0]);
$session->apply();
if (boolval(get($values, "remember"))) $session->remember();
json_response(true);
