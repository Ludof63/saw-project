<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

/**
 * a better version of echo that writes its output to a log file and in a formatted way
 * @param mixed $object
 */
function println(...$object): void
{
    $result = "";
    foreach ($object as $obj) {
        $result .= print_r($obj, true) . " ";
    }
    error_log($result);
}

/**
 * sets the status code,
 * writes the given string to the standard output
 * and then terminates the program
 * @return never
 */
function response(string $response, int $code = 200)
{
    http_response_code($code);
    echo $response;
    exit();
}

/**
 * specialization of response that encodes the given object to json
 * @return never
 * @param mixed $response
 */
function json_response($response, int $code = 200)
{
    $json = json_encode($response);
    check($json !== false);
    header("Content-type: application/json");
    response($json, $code);
}

/**
 * alternative version of response that sends a php file
 * @return never
 */
function response_page(string $page, int $code = 200)
{
    http_response_code($code);
    /**
     * @psalm-suppress UnresolvableInclude
     */
    require($page);
    exit();
}

/**
 * alternative version of response that sends a file
 * @return never
 */
function response_file(string $file, string $name)
{
    $mime = mime_content_type($file);
    header("Content-type: $mime");
    header('Content-Disposition: attachment; filename="' . $name . '"');
    $handler = fopen($file, "r");
    check($handler !== false);
    check(fpassthru($handler) !== false);
    exit();
}

/**
 * redirects a user to another page, then exits the program
 * @return never
 */
function redirect(string $page)
{
    header("Location: $page");
    response("", 302);
}

/**
 * reads a config value from the config file
 */
function get_config(string $key): string
{
    $file = file_get_contents(dirname(__FILE__) . "/../../config.json");
    check($file !== false);
    $config = json_decode($file, true);
    check(is_array($config));
    $value = $config[$key];
    check(is_string($value));
    return $value;
}

/**
 * utility function to prevent the default behaviour of php of printing
 * a warning to the standard output if an element in array doesn't exists,
 * this function insead raises an exception when no such element is found
 * @template K
 * @template V
 * @param array<K,V> $array
 * @param K $key
 * @return V
 */
function get(array $array, $key)
{
    check(array_key_exists($key, $array));
    return $array[$key];
}

/**
 * check that a value is not null and then returns it
 * @template T
 * @param T|null $value
 * @return T
 */
function not_null($value)
{
    check($value !== null);
    return $value;
}

/**
 * returns the path of an attachment file by using its id
 */
function get_attachment_file(int $id): string
{
    return ATTACHMENTS_DIR . "/$id";
}


/**
 * utility function to enforce that a condition is true,
 * raises an exception if the condition is false
 * @psalm-assert true $condition
 */
function check(bool $condition): void
{
    if (!$condition) throw new AssertionError();
}

/**
 * check if the program is running on a production server
 */
function is_local(): bool
{
    return !isset($_SERVER['HTTPS']);
}

/**
 * gets the root path of the webserver
 */
function get_root_path(): string
{
    return is_local() ? "/" : "/~S4943369";
}

/**
 * gets the origin of the webserver
 */
function get_allow_origin(): string
{
    return is_local() ? "*" : "https://saw21.dibris.unige.it";
}

/**
 * sets a cookie with the correct path and check if the set fails
 */
function set_cookie(string $name, string $value, int $expires): void
{
    check(setcookie($name, $value, $expires, get_root_path()));
}

/**
 * delete a cookie and check if the deletion failed
 */
function delete_cookie(string $name): void
{
    check(setcookie($name));
}
