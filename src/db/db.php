<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

/**
 * @psalm-type SQLType = int|string|bool|null
 */

class SQLException extends RuntimeException
{
}
class NotSingleRowException extends SQLException
{
}
class NotScalarException extends SQLException
{
}

class DB
{
    /**
     * gets the hostname of the db
     * geteven succeeds if we are inside a docker container
     */
    private static function get_db_host(): string
    {
        $host = getenv("DB_HOST");
        if (!$host) return "127.0.0.1";
        return $host;
    }
    private static function get_db_password(): string
    {
        return get_config("db_password");
    }

    private PDO $db;

    /**
     * creates a new PDO object with the correct parameters for the connection
     * then sets PDO to throw exceptions
     * finnally it sets up the db (if necessary)
     */
    public function __construct()
    {
        $host = DB::get_db_host();
        $this->db = new PDO("mysql:host=$host;dbname=S4943369", "S4943369", DB::get_db_password());
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->is_db_setup()) $this->setup_db();
    }

    /**
     * prepares PDOstatement using query and a number of params to bind to it
     * to bind params to query we need to get thier type
     * @param SQLType $params 
     */
    private function sql(string $query, ...$params): PDOStatement
    {
        $stmt = $this->db->prepare($query);
        $counter = 1;
        foreach ($params as &$value) {
            if (is_int($value)) $type = PDO::PARAM_INT;
            else if (is_bool($value)) $type = PDO::PARAM_BOOL;
            else if (is_string($value)) $type = PDO::PARAM_STR;
            else $type = PDO::PARAM_NULL;
            $stmt->bindParam($counter, $value, $type);
            $counter++;
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * produces a generator containing the result of the query
     * (to see the cursor as an iterator...gets closed when it finishes the elements)
     * @param SQLType $params
     * @return Generator<array<string|int,string|null>>
     */
    public function query(string $query, ...$params): Generator
    {
        $stmt = $this->sql($query, ...$params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $stmt->fetch()) yield $row;
        $stmt->closeCursor();
    }

    /**
     * executes a non-query statement 
     * (we return an int becuse when we execute an insert we retrieve the last id inserted, 0 otherwise)
     * @param SQLType $params
     */
    public function execute(string $stmt, ...$params): int
    {
        $this->sql($stmt, ...$params);
        return intval($this->db->lastInsertId());
    }

    /**
     * executes a query of an expected single row result
     * - if there are more than one rows it raise an exception
     * - if there are no rows it returns null
     * @param SQLType $params
     * @return array<string|int,string|null>|null
     */
    public function query_row(string $query, ...$params)
    {
        $cursor = $this->query($query, ...$params);
        $result = null;
        foreach ($cursor as $row) {
            if ($result !== null) throw new NotSingleRowException("Number of rows is greater than 1");
            $result = $row;
        }
        return $result;
    }

    /**
     * executes a query of an expected single row and single column result
     * -  if there are more than one columns it raises an exception
     * @param SQLType $params
     * @return string|null
     */
    public function query_scalar(string $query, ...$params)
    {
        $row = $this->query_row($query, ...$params);
        if ($row === null) return null;
        if (count($row) !== 1) throw new NotScalarException("This is not a scalar");
        $key = array_key_first($row);
        return $row[$key];
    }

    /**
     * controls that the query executed has at least one row result
     * @param SQLType $params
     */
    public function query_exists(string $query, ...$params): bool
    {
        try {
            return $this->query_row($query, ...$params) !== null;
        } catch (NotSingleRowException $_) {
            return false;
        }
    }

    /**
     * controls that the query executed has exactly one row
     * @param SQLType $params
     */
    public function query_exists_only(string $query, ...$params): bool
    {
        return $this->query_row($query, ...$params) !== null;
    }

    /**
     * is a generict function that casts the result of query_scalar to a specific tyèe
     * @template T
     * @param callable(string|null):T $f
     * @param SQLType $params
     * @return T|null
     */
    private function query_type(callable $f, string $query, ...$params)
    {
        $result = $this->query_scalar($query, ...$params);
        if ($result === null) return null;
        return $f($result);
    }

    /**
     * specializes query_type() for string type
     * @param SQLType $params
     * @return string|null
     */
    public function query_string(string $query, ...$params)
    {
        /** @var string|null */
        return $this->query_type(fn ($v) => $v, $query, ...$params);
    }

    /**
     * specializes query_type() for bool type
     * @param SQLType $params
     * @return bool|null
     */
    public function query_bool(string $query, ...$params)
    {
        /** @var bool|null */
        return $this->query_type(fn ($v) => boolval($v), $query, ...$params);
    }

    /**
     * tries to execute a simmple query to test if DB is setup
     */
    private function is_db_setup(): bool
    {
        return $this->query_exists_only("SELECT TABLE_NAME FROM information_schema.TABLES WHERE table_name='users'");
    }

    /**
     * use execute queries to setup the correct schema of the DB
     * and at the end inserst the admin 
     */
    public function setup_db(): void
    {
        $this->execute("CREATE TABLE users(
            id int PRIMARY KEY AUTO_INCREMENT,
            email varchar(" . strval(MAX_EMAIL_LENGTH) . ") UNIQUE NOT NULL CHECK (email REGEXP '" . strval(EMAIL_REGEX) . "'),
            username varchar(" . strval(MAX_USERNAME_LENGTH) . ") UNIQUE NOT NULL CHECK (LENGTH(username)>=" . strval(MIN_USERNAME_LENGTH) . "),
            password char(" . strval(HASHED_PASSWORD_LENGTH) . ") NOT NULL,
            firstname varchar(" . strval(MAX_NAME_LENGTH) . ") NOT NULL CHECK (LENGTH(firstname)>=" . strval(MIN_NAME_LENGTH) . "),
            lastname varchar(" . strval(MAX_NAME_LENGTH) . ") NOT NULL CHECK (LENGTH(lastname)>=" . strval(MIN_NAME_LENGTH) . "),
            bio varchar(" . strval(MAX_BIO_LENGTH) . "),
            is_admin bool NOT NULL DEFAULT false
        )");
        $this->execute("CREATE TABLE challenges(
            id int PRIMARY KEY AUTO_INCREMENT,
            challenge_name varchar(" . strval(MAX_CHALLENGE_NAME_LENGTH) . ") NOT NULL CHECK (LENGTH(challenge_name)>=" . strval(MIN_CHALLENGE_NAME_LENGTH) . "),
            points int NOT NULL CHECK (points BETWEEN 0 AND " . strval(MAX_CHALLENGE_POINTS) . "),
            category varchar(" . strval(MAX_CATEGORY_LENGTH) . ") NOT NULL CHECK (LENGTH(category)>=" . strval(MIN_CHALLENGE_POINTS) . "),
            description varchar(" . strval(MAX_CHALLENGE_DESCRIPTION_LENGTH) . ") NOT NULL,
            flag varchar(" . strval(MAX_FLAG_LENGTH) . ") NOT NULL CHECK (LENGTH(flag)>=" . strval(MIN_FLAG_LENGTH) . ")
        )");
        $this->execute("CREATE TABLE completed(
            id int PRIMARY KEY AUTO_INCREMENT,
            challenge int,
            user int,
            FOREIGN KEY (challenge) REFERENCES challenges(id) ON DELETE CASCADE,
            FOREIGN KEY (user) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE (challenge,user)
        )");
        $this->execute("CREATE TABLE attachments (
            id int PRIMARY KEY AUTO_INCREMENT,
            attachment_name varchar(" . strval(MAX_ATTACHMENT_NAME_LENGTH) . ") NOT NULL,
            challenge int NOT NULL,
            FOREIGN KEY (challenge) REFERENCES challenges(id) ON DELETE CASCADE
        )");
        $this->execute("CREATE TABLE deleted_tokens (
            id int PRIMARY KEY AUTO_INCREMENT,
            signature char(" . strval(JWT_B64_SIGNATURE_LENGTH) . ") NOT NULL,
            iat int NOT NULL
        )");
        $admin_email = get_config("admin_email");
        $admin_password = password_hash(get_config("admin_password"), PASSWORD_ALGORITHM);
        $admin_firstname = get_config("admin_firstname");
        $admin_lastname = get_config("admin_lastname");
        $this->execute("INSERT INTO users(email,username,password,firstname,lastname,is_admin) VALUES (?,'admin',?,?,?,true)", $admin_email, $admin_password, $admin_firstname, $admin_lastname);
    }
}
