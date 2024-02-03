<?php

declare(strict_types=1);

require_once(__DIR__ . "/../php/imports.php");

/**
 * sanitizes the input by checking that the required fields exists and that they satisfy certain properties
 * @param array<string|int,callable(string):bool> $validations map that associates each form input to a validation function
 * @param array<string|int,string> $defaults values that gets used if not present when submitting the form 
 * @return array<string|int,string> array containing for each form input its validated value
 */
function validate(array $validations, bool $post = true, array $defaults = []): array
{
    $result = array();
    $method = get($_SERVER, "REQUEST_METHOD");
    if (!is_string($method)) throw new BadRequestException();
    $expected_method = $post ? "POST" : "GET";
    if ($method !== $expected_method) throw new WrongMethodError($method, $expected_method);
    $src = $post ? $_POST : $_GET;
    foreach ($validations as $key => $validation) {
        if (!array_key_exists($key, $src)) {
            if (array_key_exists($key, $defaults)) {
                $result[$key] = $defaults[$key];
                continue;
            } else throw new FieldNotSetError(strval($key));
        } else $value = $src[$key];
        if (!is_string($value)) throw new FieldIsNotStringError(strval($key));
        if (!$validation($value)) throw new FunctionValidationError(strval($key), $value);
        $result[$key] = $value;
    }
    return $result;
}

function validate_password(string $value): bool
{
    return strlen($value) >= MIN_PASSWORD_LENGTH;
}

function validate_name(string $value): bool
{
    return strlen($value) >= MIN_NAME_LENGTH && strlen($value) <= MAX_NAME_LENGTH;
}

function validate_email(string $value): bool
{
    return !empty(filter_var($value, FILTER_VALIDATE_EMAIL));
}

function validate_username(string $value): bool
{
    return strlen($value) >= MIN_USERNAME_LENGTH && strlen($value) <= MAX_USERNAME_LENGTH;
}

function validate_bio(string $value): bool
{
    return strlen($value) <= MAX_BIO_LENGTH;
}

function validate_challenge_name(string $value): bool
{
    return strlen($value) >= MIN_CHALLENGE_NAME_LENGTH && strlen($value) <= MAX_CHALLENGE_NAME_LENGTH;
}

function validate_challenge_description(string $value): bool
{
    return strlen($value) <= MAX_CHALLENGE_DESCRIPTION_LENGTH;
}

function validate_challenge_points(string $value): bool
{
    $value = filter_var($value, FILTER_VALIDATE_INT);
    if (!$value) return false;
    $v = intval($value);
    return $v >= MIN_CHALLENGE_POINTS;
}

function validate_id(string $value): bool
{
    $value = filter_var($value, FILTER_VALIDATE_INT);
    if (!$value) return false;
    $v = intval($value);
    return $v >= 0 && $v <= MAX_CHALLENGE_POINTS;
}

function validate_bool(string $value): bool
{
    return $value === "true" || $value === "false";
}

function validate_flag(string $value): bool
{
    return strlen($value) >= MIN_FLAG_LENGTH && strlen($value) <= MAX_FLAG_LENGTH;
}

function validate_nothing(string $_): bool
{
    return true;
}

function validate_challenge_category(string $value): bool
{
    return strlen($value) >= MIN_CATEGORY_LENGTH && strlen($value) <= MAX_CATEGORY_LENGTH;
}

/**
 * check that a file input was correctly uploaded
 * @param string $name the name of the form input of an uploaded file
 * @return array{string,string} a couple containing the name of the uploaded file and its path
 */
function validate_file(string $name): array
{
    if (!array_key_exists($name, $_FILES)) throw new FieldNotSetError($name);
    $files = $_FILES[$name];
    if (!is_array($files)) throw new BadRequestException();
    $name = get($files, "name");
    if (!is_string($name)) throw new BadRequestException();
    $tmp = get($files, "tmp_name");
    if (!is_string($tmp)) throw new BadRequestException();
    $error = get($files, "error");
    if ($error !== UPLOAD_ERR_OK) {
        check(is_int($error));
        throw new FileUploadError(strval($error));
    }
    if (strlen($name) > MAX_ATTACHMENT_NAME_LENGTH) throw new ValidationError("File name too long");
    return [$name, $tmp];
}
