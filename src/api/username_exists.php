<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

$validations = ["username" => "validate_username"];
$values = validate($validations, false);
if (username_exists($db, get($values, "username"))) json_response(true);
else json_response(false);
