<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

$validations = ["email" => "validate_email"];
$values = validate($validations, false);
if (email_exists($db, get($values, "email"))) json_response(true);
else json_response(false);
