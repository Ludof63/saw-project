<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

Session::require_admin();
$validations = ["id" => "validate_id"];
$values = validate($validations);
remove_challenge($db, intval(get($values, "id")));
json_response(true);
