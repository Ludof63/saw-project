<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

$session = Session::require_login();
$validations = ["challenge" => "validate_id", "flag" => "validate_flag"];
$values = validate($validations);
$result = complete_challenge($db, $session->get_id(), intval(get($values, "challenge")), get($values, "flag"));
json_response($result);
