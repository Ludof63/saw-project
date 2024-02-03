<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);


$s = Session::require_login();

$validators = [
    "email" => "validate_email",
    "username" => "validate_username",
    "firstname" => "validate_name",
    "lastname" => "validate_name",
    "bio" => "validate_bio",
];
$values = validate($validators, true, ["username" => "", "bio" => ""]);

check(array_key_exists("username", $values));
if ($values["username"] === "") $values["username"] = get($values, "email");

$db = new DB();
update_user_info($db, $s->get_id(), get($values, "email"), get($values, "username"), get($values, "firstname"), get($values, "lastname"), get($values, "bio"));
$s->set_username(get($values, "username"));

json_response(["status" => "ok"]);
