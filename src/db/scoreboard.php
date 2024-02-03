<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

/**
 * @param string|null $search
 * @return Generator<array{int,string,int,int,int}>
 */
function get_scoreboard(DB $db, $search): Generator
{
    $stmt = "SELECT users.id as id,users.username as username,sum(challenges.points) as points,count(challenges.id) as count,row_number() OVER (ORDER BY sum(challenges.points) DESC) AS place
            FROM users
            LEFT JOIN completed ON completed.user=users.id
            LEFT JOIN challenges ON completed.challenge=challenges.id
            WHERE users.is_admin=false
            GROUP BY users.id,users.username
            ORDER BY points DESC";
    if ($search !== null) $stmt = "SELECT * FROM ($stmt) as users WHERE username LIKE CONCAT('%',?,'%')";
    $generator = $db->query($stmt, $search);
    foreach ($generator as $row) {
        yield [intval(get($row, "id")), not_null(get($row, "username")), intval(get($row, "points")), intval(get($row, "count")), intval(get($row, "place"))];
    }
}
