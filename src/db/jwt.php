<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

/**
 * check that the signature of the jwt is in the deleted_tokens
 * - a tokens is invalid (is in deleted_tokens) if the user logout before their token lifespan is over
 */
function is_jwt_invalid(DB $db, string $signature): bool
{
    return $db->query_exists("SELECT id FROM deleted_tokens WHERE signature=?", $signature);
}

/**
 * invalidate a jwt adding it to deleted_tokens (the iat of it is useful for the cleanup)ù
 * - a tokens is invalid (is in deleted_tokens) if the user logout before their token lifespan is over
 */
function invalidate_jwt(DB $db, string $signature, int $iat): void
{
    $db->execute("INSERT INTO deleted_tokens(signature,iat) VALUES (?,?)", $signature, $iat);
}

/**
 * removes from the invalid jwts the ones which lifespan would be over now
 * - a tokens is invalid (is in deleted_tokens) if the user logout before their token lifespan is over
 */
function cleanup_jwts(DB $db): void
{
    $db->execute("DELETE FROM deleted_tokens WHERE iat+" . strval(TOKEN_LIFESPAN) . "<unix_timestamp(current_timestamp())");
}
