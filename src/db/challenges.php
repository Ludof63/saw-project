<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

function insert_challenge(DB $db, string $challenge_name, int $points, string $category, string $description, string $flag): int
{
    return $db->execute("INSERT INTO challenges(challenge_name,points,category,description,flag) VALUES (?,?,?,?,?)", $challenge_name, $points, $category, $description, $flag);
}

/**
 * @return Generator<array{int,string,int,string,int}>
 */
function list_challenges(DB $db): Generator
{
    $challenges = $db->query("SELECT challenges.id,challenges.challenge_name,challenges.points,challenges.category,count(completed.id) as solved
                              FROM challenges
                              LEFT JOIN (SELECT completed.id,completed.challenge FROM completed JOIN users ON completed.user=users.id WHERE NOT users.is_admin) AS completed ON completed.challenge=challenges.id
                              GROUP BY challenges.id
                              ORDER BY challenges.challenge_name");
    foreach ($challenges as $challenge) yield [intval(get($challenge, "id")), not_null(get($challenge, "challenge_name")), intval(get($challenge, "points")), not_null(get($challenge, "category")), intval(get($challenge, "solved"))];
}


function edit_challenge(DB $db, int $id, string $challenge_name, int $points, string $category, string $description, string $flag): void
{
    $db->execute("UPDATE challenges SET challenge_name=?,points=?,category=?,description=?,flag=? WHERE id=?", $challenge_name, $points, $category, $description, $flag, $id);
}

/**
 * @return array{string,string}|null
 */
function get_challenge_details(DB $db, int $id)
{
    $result = $db->query_row("SELECT description,flag FROM challenges WHERE id=?", $id);
    if ($result === null) return null;
    return [not_null(get($result, "description")), not_null(get($result, "flag"))];
}

function remove_challenge(DB $db, int $challenge): void
{
    remove_all_attachments($db, $challenge);
    $db->execute("DELETE FROM challenges WHERE id=?", $challenge);
}
