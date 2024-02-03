<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");
entrypoint(true);

$db = new DB();

$session = Session::require_login();
$validations = ["id" => "validate_id"];
$values = validate($validations, false);
$challenge_id = intval(get($values, "id"));
$result = get_challenge_details($db, $challenge_id);
if ($result === null) json_response(["status" => "error"]);
$attachments = list_attachments($db, $challenge_id);
$response = ["status" => "ok", "description" => $result[0], "attachments" => $attachments];
if ($session->is_admin()) $response["flag"] = $result[1];
json_response($response);
