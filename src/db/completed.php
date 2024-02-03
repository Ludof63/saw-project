<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");


function complete_challenge(DB $db, int $user, int $challenge, string $flag): int
{
    try {
        $result = $db->execute("INSERT INTO completed(user,challenge) SELECT ?,id FROM challenges WHERE id=? AND flag=?", $user, $challenge, $flag);
    } catch (PDOException $e) {
        if (is_duplicate_key_exception($e)) return CHALLENGE_ALREADY_SOLVED;
        throw $e;
    }
    return $result !== 0 ? CHALLENGE_SOLVED : INVALID_FLAG;
}
