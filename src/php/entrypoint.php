<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");


/**
 * main function that setups all special php settings
 * - sets the error reporting file
 * - prepares the exception handler
 * - starts the php_session
 * - sets security headers
 * @param bool $ajax enable specific settings for ajax calls
 */
function entrypoint(bool $ajax = false): void
{
    error_reporting(E_ALL);
    ini_set("error_log", __DIR__ . "/../../logs.txt");
    prepare_exception_handler($ajax);
    session();
    header("Content-Security-Policy: default-src 'none'; script-src-elem 'self'; style-src 'self' https://fonts.googleapis.com/css2; font-src 'self' fonts.gstatic.com; img-src 'self'; connect-src 'self'");
    if ($ajax) header("Access-Control-Allow-Origin: " . get_allow_origin());
}
