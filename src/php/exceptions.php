<?php

require_once(__DIR__ . "/../php/imports.php");

class HttpClientException extends Exception
{
}

class UnauthorizedException extends HttpClientException
{
}

class ForbiddenException extends HttpClientException
{
}

class NotFoundException extends HttpClientException
{
}

class BadRequestException extends HttpClientException
{
}

class InvalidJWTException extends BadRequestException
{
}

class ValidationError extends BadRequestException
{
}

class WrongMethodError extends ValidationError
{
    public function __construct(string $method, string $expected)
    {
        parent::__construct("Got '$method' expected '$expected'");
    }
}

class FileUploadError extends ValidationError
{
}

class PasswordConfirmMismatchError extends ValidationError
{
}

class FieldNotSetError extends ValidationError
{
    public function __construct(string $field_name)
    {
        parent::__construct("Field $field_name is null");
    }
}

class FieldIsNotStringError extends ValidationError
{
    public function __construct(string $field_name)
    {
        parent::__construct("Field $field_name is not a string");
    }
}

class FunctionValidationError extends ValidationError
{
    public function __construct(string $field_name, string $value)
    {
        parent::__construct("Field $field_name with value $value didn't pass its validation");
    }
}


/**
 * sets a custom exception hadler that for every family of exception sends a correct response
 * using ajax differtiatiates the response because if the request was an ajax the client expects a json response
 */
function prepare_exception_handler(bool $ajax): void
{
    set_exception_handler(function (Throwable $exception) use ($ajax): void {
        print_exception($exception);
        if ($exception instanceof BadRequestException) {
            json_response("bad_request", 400);
        }
        if ($exception instanceof UnauthorizedException) {
            if ($ajax) json_response("unauthorized", 401);
            redirect("./");
        }
        if ($exception instanceof ForbiddenException) {
            if ($ajax) json_response("forbidden", 403);
            redirect("./");
        }
        if ($exception instanceof NotFoundException) {
            if ($ajax) json_response("not_found", 404);
            response_page(__DIR__ . "/../templates/not_found.php", 404);
        }
        if ($ajax) json_response("error", 500);
        response_page(__DIR__ . "/../templates/server_error.php", 500);
    });
}


/**
 * custom trace line for the exception, used in print_exception (improves development experience)
 * @param array{args?: array<array-key, mixed>, class?: class-string, file?: string, function?: string, line?: int, type?: "->"|"::"} $trace
 */
function get_trace_line(array $trace): string
{
    $file = array_key_exists("file", $trace) ? $trace["file"] : "unknown";
    $line = array_key_exists("line", $trace) ? $trace["line"] : "unknown";
    $function = array_key_exists("function", $trace) ? $trace["function"] : "unknown";
    $class = array_key_exists("class", $trace) ? $trace["class"] : null;
    $type = array_key_exists("type", $trace) ? $trace["type"] : "?";
    $args = array_key_exists("args", $trace) ? $trace["args"] : null;
    $fargs = "";
    if ($args !== null) {
        $first = true;
        foreach ($args as $arg) {
            if ($first) $first = false;
            else $fargs .= ",";
            $fargs .= str_replace(" ", "", str_replace("\n", "", print_r($arg, true)));
        }
    }
    if ($class !== null) $function = "$class$type$function";
    return "  File \"$file\", line $line, in $function($fargs)";
}

/**
 * a function to print better an exception (improves development experience)
 */
function print_exception(Throwable $exception): void
{
    $type = get_class($exception);
    $result = "\nTraceback (most recent call last):";
    foreach (array_reverse($exception->getTrace()) as $trace) {
        $line = get_trace_line($trace);
        $result = "$result\n$line";
    }
    $lastline = get_trace_line(["file" => $exception->getFile(), "function" => "throw $type", "line" => $exception->getLine()]);
    $result = "$result\n$lastline";
    $message = $exception->getMessage();
    $result = "$result\n$type: $message";
    println($result);
}

/**
 * checks if the exception thrown by PDO is caused by a violation of key constraint
 */
function is_duplicate_key_exception(PDOException $e): bool
{
    return count($e->errorInfo) > 1 && $e->errorInfo[1] === MYSQL_DUPLICATE_KEY_CODE;
}
