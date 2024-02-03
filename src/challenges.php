<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

$session = Session::require_login();

$db = new DB();


/** @var array<int|string,array<array{int,string,int,int,bool}>> */
$categories = [];
foreach (get_user_challenges($db, $session->get_id()) as [$id, $challenge_name, $points, $category, $count, $solved]) {
    if (!array_key_exists($category, $categories)) {
        $categories[$category] = [];
    }
    $l = [$id, $challenge_name, $points, $count, $solved];
    $categories[$category][] = $l;
}

try {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php require_once(__DIR__ . "/templates/head.php"); ?>
        <script src="js/challenges.js" type="module"></script>
    </head>

    <body>
        <?php require_once(__DIR__ . "/templates/navbar.php"); ?>

        <main class="responsive">
            <?php foreach ($categories as $category => $challenges) { ?>
                <section>
                    <div class="medium-space"></div>
                    <header>
                        <h5 class="bold capitalize"> <?= htmlspecialchars(strval($category)) ?> </h5>
                    </header>

                    <div class="grid">
                        <?php foreach ($challenges as [$id, $name, $points, $count, $solved]) { ?>
                            <div class="s12 m6 l4">
                                <article class="border round <?= $solved ? "green" : "" ?>" id="<?= $id ?>">
                                    <div>
                                        <h5 id="<?= $id ?>_name"><?= htmlspecialchars($name) ?></h5>

                                    </div>
                                    <div class="row">
                                        <a class="chip fill">
                                            <span id="<?= $id ?>_category"><?= $category ?></span>
                                        </a>
                                        <a class="chip fill">
                                            <i class="fa fa-trophy" aria-hidden="true"></i>
                                            <span id="<?= $id ?>_points"><?= $points ?></span>
                                        </a>
                                        <a class="chip fill">
                                            <i class="fa fa-flag" aria-hidden="true"></i>
                                            <span id="<?= $id ?>_count"><?= $count ?></span>
                                        </a>
                                    </div>
                                </article>

                            </div>
                        <?php } ?>
                    </div>
                    <div class="large-space"></div>
                </section>
            <?php } ?>
        </main>

        <div class="modal large-width" id="modal">
            <div class="row">
                <div class="max"></div>
                <h5 id="name">Challenge name</h5>
                <div class="max"></div>
            </div>
            <div class="small-space"></div>

            <p id="description">Challenge description</p>

            <div id="attachments">
                <div id="template">
                    <a class="button" download></a>
                    <div class="small-space"></div>
                </div>
            </div>

            <div class="small-space"></div>

            <form id="form">
                <div class="field label border" id="flag_div">
                    <input type="text" id="flag">
                    <label for="flag">Flag</label>
                    <span class="error invisible" id="flag_error_min_length">The flag is too short</span>
                    <span class="error invisible" id="flag_error_max_length">The flag is too long</span>
                    <span class="error invisible" id="flag_error_wrong">Wrong flag</span>
                    <span class="error invisible" id="flag_error_already_solved">Challenge already solved</span>
                </div>

                <nav class="center-align">
                    <button type="button" class="border" data-ui="#modal">Cancel</button>
                    <button>Submit</button>
                </nav>
            </form>
        </div>
    </body>

    </html>

<?php
} catch (Throwable $e) {
    print_exception($e);
}
