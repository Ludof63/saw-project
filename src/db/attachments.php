<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");


function add_attachment(DB $db, string $name, int $challenge, string $tmp_path): void
{
    $id = $db->execute("INSERT INTO attachments (attachment_name,challenge) VALUES (?,?)", $name, $challenge);
    $file = get_attachment_file($id);
    check(move_uploaded_file($tmp_path, $file));
}

function remove_attachment(DB $db, int $id): bool
{
    $db->execute("DELETE FROM attachments WHERE id=?", $id);
    $file = ATTACHMENTS_DIR . "/$id";
    return unlink($file);
}

function remove_all_attachments(DB $db, int $challenge): void
{
    $attachments = list_attachments($db, $challenge);
    foreach ($attachments as $id => $_) {
        $dir = ATTACHMENTS_DIR . "/$id";
        check(unlink($dir));
    }
}

/**
 * @return array<int,string>
 */
function list_attachments(DB $db, int $challenge): array
{
    $cursor = $db->query("SELECT id,attachment_name FROM attachments WHERE challenge=? ORDER BY attachment_name", $challenge);
    $result = [];
    foreach ($cursor as $row) {
        $result[intval(get($row, "id"))] = not_null(get($row, "attachment_name"));
    }
    return $result;
}

function get_attachment_name(DB $db, int $id): string
{
    return not_null($db->query_string("SELECT attachment_name FROM attachments WHERE id=?", $id));
}
