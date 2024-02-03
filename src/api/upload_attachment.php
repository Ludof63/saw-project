<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

Session::require_admin();
$validations = ["challenge_id" => "validate_id"];
$values = validate($validations);
[$name, $tmp] = validate_file("file");
add_attachment($db, $name, intval(get($values, "challenge_id")), $tmp);
json_response(true);
