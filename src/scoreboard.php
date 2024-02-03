<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

Session::require_login();

$validators = [
    "user" => "validate_nothing",
];
try {
    $values = validate($validators, false, ["user" => ""]);
} catch (ValidationError $e) {
    print_exception($e);
    throw new NotFoundException();
}

$search = get($values, "user");
if ($search === "") $search = null;

$scoreboard = get_scoreboard(new DB(), $search);

try {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php require_once(__DIR__ . "/templates/head.php"); ?>
    </head>

    <body>
        <?php require_once(__DIR__ . "/templates/navbar.php"); ?>

        <main class="responsive">
            <form id="search_form">
                <div class="row">
                    <div class="field label prefix border medium-width small">
                        <i>search</i>
                        <input type="text" id="search" name="user" value="<?= htmlspecialchars($search === null ? "" : $search) ?>">
                        <label for="search">Find user</label>
                    </div>
                    <button>Search<i>search</i></button>
                </div>
            </form>
            <table class="border" id="scoreboard">
                <thead>
                    <tr>
                        <th>Place</th>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Solved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scoreboard as [$id, $username, $score, $solved, $place]) { ?>
                        <tr>
                            <td><?= $place ?></td>
                            <td><a class="link" href="show_profile.php?user=<?= $id ?>" target="_blank"><?= htmlspecialchars($username) ?></a></td>
                            <td><?= $score ?></td>
                            <td><?= $solved ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>

    </body>

    </html>

<?php
} catch (Throwable $e) {
    print_exception($e);
}
