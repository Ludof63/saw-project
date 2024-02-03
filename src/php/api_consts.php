<?php

declare(strict_types=1);

define("MAX_EMAIL_LENGTH", 254);
define("MIN_USERNAME_LENGTH", 1);
define("MAX_USERNAME_LENGTH", 30);
define("MIN_NAME_LENGTH", 1);
define("MAX_NAME_LENGTH", 30);
define("MAX_BIO_LENGTH", 400);
define("MIN_ATTACHMENT_NAME_LENGTH", 1);
define("MAX_ATTACHMENT_NAME_LENGTH", 25);
define("MIN_CHALLENGE_NAME_LENGTH", 1);
define("MAX_CHALLENGE_NAME_LENGTH", 30);
define("MAX_CHALLENGE_DESCRIPTION_LENGTH", 1000);
define("MIN_PASSWORD_LENGTH", 6);
define("MIN_CATEGORY_LENGTH", 1);
define("MAX_CATEGORY_LENGTH", 10);
define("MIN_FLAG_LENGTH", 1);
define("MAX_FLAG_LENGTH", 50);
define("MIN_CHALLENGE_POINTS", 0);
define("MAX_CHALLENGE_POINTS", 1000);
define("EMAIL_REGEX", '^[a-zA-Z0-9]+(?:\\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(?:\\.[a-zA-Z0-9]+)*$');
define("MAX_ATTACHMENT_SIZE", 20);
define("CHALLENGE_SOLVED", 0);
define("INVALID_FLAG", 1);
define("CHALLENGE_ALREADY_SOLVED", 2);
