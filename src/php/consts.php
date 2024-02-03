<?php

declare(strict_types=1);

define("HASHED_PASSWORD_LENGTH", 60);
define("PASSWORD_ALGORITHM", PASSWORD_BCRYPT);
define("MYSQL_DUPLICATE_KEY_CODE", 1062);

define("TOKEN_LIFESPAN", 15 * 24 * 60 * 60);
define("JWT_B64_SIGNATURE_LENGTH", 43);
define("ATTACHMENTS_DIR", __DIR__ . "/../../attachments");
define("REMEMBER_COOKIE_NAME", "S4943369.remember");
define("SESSION_ID", "S4943369.id");
define("SESSION_USERNAME", "S4943369.username");
define("SESSION_ADMIN", "S4943369.admin");
