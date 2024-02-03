<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

$session = Session::require_login();
$validations = ["pass" => "validate_password"];
$values = validate($validations);
$hash = password_hash(get($values, "pass"), PASSWORD_ALGORITHM);
check($hash !== false);
update_user_password($db, $session->get_id(), $hash);
json_response(true);
