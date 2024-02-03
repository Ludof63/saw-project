<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$validators = [
    "email" => "validate_email",
    "username" => "validate_username",
    "pass" => "validate_password",
    "firstname" => "validate_name",
    "lastname" => "validate_name",
    "confirm" => "validate_password",
    "remember" => "validate_bool",
];
$values = validate($validators, true, ["remember" => "false", "username" => ""]);

if (get($values, "username") === "") $values["username"] = get($values, "email");

if (get($values, "confirm") != get($values, "pass")) throw new PasswordConfirmMismatchError();

$hash = password_hash(get($values, "pass"), PASSWORD_ALGORITHM);
check($hash !== false);
$db = new DB();
$user = register_user($db, get($values, "email"), get($values, "username"), $hash, get($values, "firstname"), get($values, "lastname"));

$session = Session::from_id($db, $user);
$session->apply();
if (boolval(get($values, "remember"))) $session->remember();
json_response(["status" => "ok"]);
