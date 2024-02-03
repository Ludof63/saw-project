<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

Session::require_login();

$validators = [
    "id" => "validate_id",
];
$values = validate($validators, false);

$id = intval(get($values, "id"));

$file = get_attachment_file($id);

if (!file_exists($file)) throw new NotFoundException();

$db = new DB();

$name = get_attachment_name($db, $id);

response_file($file, $name);
