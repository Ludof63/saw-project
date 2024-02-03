<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

/**
 * @return array{int,string}|null
 */
function get_user(DB $db, string $email)
{
    $result = $db->query_row("SELECT id,password FROM users WHERE email=?", $email);
    if ($result === null) return null;
    return [intval(get($result, "id")), not_null(get($result, "password"))];
}




function register_user(DB $db, string $email, string $username, string $password, string $firstname, string $lastname): int
{
    try {
        return $db->execute("INSERT INTO users(email,username,password,firstname,lastname) VALUES (?,?,?,?,?)", $email, $username, $password, $firstname, $lastname);
    } catch (PDOException $e) {
        if (is_duplicate_key_exception($e)) {
            throw new BadRequestException("username or email already taken");
        }
        throw $e;
    }
}

function update_user_info(DB $db, int $id, string $email, string $username, string $firstname, string $lastname, string $bio): void
{
    $db->execute("UPDATE users SET email=?,username=?,firstname=?,lastname=?,bio=? WHERE id=?", $email, $username, $firstname, $lastname, $bio, $id);
}

function update_user_password(DB $db, int $id, string $pass): void
{
    $db->execute("UPDATE users SET password=? WHERE id=?", $pass, $id);
}

function username_exists(DB $db, string $username): bool
{
    return $db->query_exists_only("SELECT id FROM users WHERE username=?", $username);
}

function email_exists(DB $db, string $email): bool
{
    return $db->query_exists_only("SELECT id FROM users WHERE email=?", $email);
}

/**
 * @return array{string,string,string,string,null|string,int,int}
 */
function get_user_info(DB $db, int $id)
{
    $result = $db->query_row("SELECT users.email, users.username, users.firstname, users.lastname, users.bio,  COUNT(challenges.id) AS cnt,SUM(challenges.points) AS score
                            FROM users
                            LEFT JOIN completed ON completed.user=users.id
                            LEFT JOIN challenges ON completed.challenge=challenges.id
                            WHERE users.id=?", $id);
    check($result !== null);
    $email = get($result, "email");
    if ($email === null) throw new NotFoundException();
    return [not_null(get($result, "email")), not_null(get($result, "username")), not_null(get($result, "firstname")), not_null(get($result, "lastname")), get($result, "bio"), intval(get($result, "cnt")), intval(get($result, "score"))];
}


/**
 * @return Generator<array{int,string,int,string,int,bool}>
 */
function get_user_challenges(DB $db, int $id): Generator
{
    $challenges = $db->query("SELECT challenges.id,challenges.challenge_name,challenges.points,challenges.category,count.count,completed.user as solved
                              FROM challenges
                              LEFT JOIN
                              (
                                SELECT count(completed.id) AS count,completed.challenge
                                FROM (SELECT completed.id,completed.challenge FROM completed JOIN users ON completed.user=users.id WHERE NOT users.is_admin) AS completed
                                GROUP BY completed.challenge
                              ) AS count ON count.challenge=challenges.id
                              LEFT JOIN
                              (
                                SELECT completed.user,completed.challenge FROM completed WHERE completed.user=?
                              ) AS completed ON completed.challenge=challenges.id
                              ORDER BY challenges.category,challenges.points,challenges.challenge_name", $id);
    foreach ($challenges as $challenge) {
        $solved = get($challenge, "solved");
        if ($solved !== null) check(intval($solved) == $id);
        yield [intval(get($challenge, "id")), not_null(get($challenge, "challenge_name")), intval(get($challenge, "points")), not_null(get($challenge, "category")), intval(get($challenge, "count")), $solved !== null];
    }
}

/**
 * @return bool|null
 */
function is_admin(DB $db, int $user)
{
    return $db->query_bool("SELECT is_admin FROM users WHERE id=?", $user);
}

/**
 * @return string|null
 */
function get_username(DB $db, int $user)
{
    return $db->query_string("SELECT username FROM users WHERE id=?", $user);
}
