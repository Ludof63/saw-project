<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

class Session
{
    private int $id;
    private string $username;
    private bool $is_admin;

    /**
     * return a session if the user is logged (is using a valid session)
     * raise an exception otherwise
     */
    public static function require_login(): Session
    {
        $session = Session::from_session();
        if ($session === null) throw new UnauthorizedException("Session not found");
        return $session;
    }

    /**
     * check if the user is logged and is admin
     * raise an exception if one of the two controls fails
     */
    public static function require_admin(): void
    {
        $session = Session::require_login();
        if (!$session->is_admin()) throw new ForbiddenException("Is not admin");
    }

    /**
     * retrieves details saved in $_SESSION and checks them
     * returns a new Session if all the checks pass 
     * returns null if a session is not yet set
     * 
     * @return Session|null
     */
    public static function from_session()
    {
        check(isset($_SESSION));
        if (!array_key_exists(SESSION_ID, $_SESSION)) return null;
        $id = get($_SESSION, SESSION_ID);
        $username = get($_SESSION, SESSION_USERNAME);
        $admin = get($_SESSION, SESSION_ADMIN);
        check(is_int($id));
        check(is_bool($admin));
        return new Session($id, $admin, $username);
    }

    /**
     * checks id the client is logged -> has a valid session
     */
    public static function is_logged(): bool
    {
        return Session::from_session() !== null;
    }

    /**
     * creates a new Session from the id of the user 
     */
    public static function from_id(DB $db, int $id): Session
    {
        $username = get_username($db, $id);
        $admin = is_admin($db, $id);
        check($admin !== null);
        check($username !== null);
        return new Session($id, $admin, $username);
    }


    public function __construct(int $id, bool $is_admin, string $username)
    {
        $this->id = $id;
        $this->is_admin = $is_admin;
        $this->username = $username;
    }

    /**
     * applies the Session , sets to Session values the $_SESSION
     */
    public function apply(): void
    {
        $_SESSION[SESSION_ID] = $this->id;
        $_SESSION[SESSION_USERNAME] = $this->username;
        $_SESSION[SESSION_ADMIN] = $this->is_admin;
    }

    /**
     * sets the jwt for the session
     */
    public function remember(): void
    {
        set_cookie(REMEMBER_COOKIE_NAME, strval(JWT::from_id($this->id)), time() + TOKEN_LIFESPAN);
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_username(): string
    {
        return $this->username;
    }

    /**
     * changes username in Session and also applies it too
     */
    public function set_username(string $new_username): void
    {
        $this->username = $new_username;
        $this->apply();
    }

    public function is_admin(): bool
    {
        return $this->is_admin;
    }
}


/**
 * "replacement of session_start()" that checks, if the user hasn't a valid session, 
 * if it has a valid jwt and if it does it creates a session from it
 */
function session(): void
{
    check(session_start());
    if (!Session::is_logged() && array_key_exists(REMEMBER_COOKIE_NAME, $_COOKIE)) {
        [$jwt, $signature] = JWT::from_string(get($_COOKIE, REMEMBER_COOKIE_NAME));
        $db = new DB();
        if ($jwt->is_valid($db, $signature)) {
            $user = $jwt->get_id();
            $username = get_username($db, $user);
            $admin = is_admin($db, $user);
            if ($username === null) return;
            if ($admin === null) return;
            $session = new Session($user, $admin, $username);
            $session->apply();
            cleanup_jwts($db);
        }
    }
}

/**
 * "replacement of session_destory()" that deletes also the jwt, rememberme cookie
 */
function delete_session(): void
{
    if (array_key_exists(REMEMBER_COOKIE_NAME, $_COOKIE)) {
        $db = new DB();
        [$jwt, $signature] = JWT::from_string(get($_COOKIE, REMEMBER_COOKIE_NAME));
        if ($jwt->is_valid($db, $signature)) {
            $iat = $jwt->get_iat();
            invalidate_jwt($db, $signature, $iat);
        }
        delete_cookie(REMEMBER_COOKIE_NAME);
    }
    check(session_destroy());
}
