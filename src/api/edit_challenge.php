<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

Session::require_admin();
$validations = ["id" => "validate_id", "name" => "validate_challenge_name", "points" => "validate_challenge_points", "category" => "validate_challenge_category", "description" => "validate_challenge_description", "flag" => "validate_flag"];
$values = validate($validations);
edit_challenge($db, intval(get($values, "id")), get($values, "name"), intval(get($values, "points")), get($values, "category"), get($values, "description"), get($values, "flag"));
json_response(true);
