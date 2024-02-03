<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

$session = Session::require_login();
$user = $session->get_id();

$validators = [
    "user" => "validate_id",
];
try {
    $values = validate($validators, false, ["user" => strval($user)]);
} catch (ValidationError $e) {
    print_exception($e);
    throw new NotFoundException();
}
$user = intval(get($values, "user"));

$show_edit = ($user == $session->get_id());


$db = new DB();

$user_info = get_user_info($db, $user);
[$email, $username, $first_name, $last_name, $bio, $challenge_count, $score] = $user_info;

$challenges = get_user_challenges($db, $user);

try {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php require_once(__DIR__ . "/templates/head.php"); ?>
        <?php if ($show_edit) { ?>
            <script src="js/profile.js" type="module"></script>
        <?php } ?>
    </head>

    <body>
        <?php require_once(__DIR__ . "/templates/navbar.php"); ?>

        <div class="primary">
            <div class="medium-space"></div>
            <div class="row">
                <div class="max"></div>
                <h4 class="capitalize"><?= htmlspecialchars($first_name) ?> <?= htmlspecialchars($last_name) ?></h4>
                <div class="max"></div>
            </div>
            <div class="row">
                <div class="max"></div>
                <h5 class="lower">@<?= htmlspecialchars($username) ?></h5>
                <div class="max"></div>
            </div>
            <div class="row">
                <div class="max"></div>
                <a class="chip fill">
                    <i class="fa fa-flag" aria-hidden="true"></i>
                    <span><?= $challenge_count ?></span>
                </a>
                <a class="chip fill">
                    <i class="fa fa-trophy" aria-hidden="true"></i>
                    <span><?= $score ?></span>
                </a>
                <div class="max"></div>
            </div>
            <div class="medium-space"></div>
        </div>
        <div class="medium-space"></div>



        <main class="responsive">

            <article class="round border">
                <form id="update_form">
                    <div class="center-align ">
                        <i class="fa fa-user fa-3x overflow"></i>
                    </div>

                    <div class="small-space"></div>

                    <div class="grid">
                        <div class="s12 l6">
                            <div class="field label border center large-width" id="update_firstname_div">
                                <input type="text" id="update_firstname" disabled value="<?= htmlspecialchars($first_name) ?>">
                                <label for="update_firstname">First name</label>
                                <span class="error invisible" id="update_firstname_error_min_length">Your first name is too short</span>
                                <span class="error invisible" id="update_firstname_error_max_length">Your first name is too long</span>
                            </div>

                            <div class="field label border center large-width" id="update_lastname_div">
                                <input type="text" id="update_lastname" disabled value="<?= htmlspecialchars($last_name) ?>">
                                <label for="update_lastname">Last name</label>
                                <span class="error invisible" id="update_lastname_error_min_length">Your last name is too short</span>
                                <span class="error invisible" id="update_lastname_error_max_length">Your last name is too long</span>
                            </div>

                            <div class="field label border large-width center" id="update_username_div">
                                <input type="text" id="update_username" disabled value="<?= htmlspecialchars($username) ?>">
                                <label for="update_username">Username</label>
                                <span class="error invisible" id="update_username_error_min_length">Your username is too short</span>
                                <span class="error invisible" id="update_username_error_max_length">Your username is too long</span>
                                <span class="error invisible" id="update_username_error_username_exists">Username already taken</span>
                            </div>

                            <?php if ($show_edit) { ?>
                                <div class="field label border large-width center" id="update_email_div">
                                    <input type="email" id="update_email" disabled value="<?= htmlspecialchars($email) ?>">
                                    <label for="update_email">Email</label>
                                    <span class="error invisible" id="update_email_error_email_format">Your email is not valid</span>
                                    <span class="error invisible" id="update_email_error_email_exists">Email is already used</span>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="s12 l6">
                            <div class="field textarea label border large-width center small-height top-align" id="update_bio_div">
                                <textarea type="text" id="update_bio" disabled> <?= $bio === null ? "" : htmlspecialchars($bio) ?></textarea>
                                <label for="update_bio">Bio</label>
                                <span class="error invisible" id="update_bio_error_max_length">Your bio is too long</span>
                            </div>

                            <div class="small-space"></div>
                            <?php if ($show_edit) { ?>
                                <button type="button" class="center" data-ui="#change_password_modal">Change Password</button>
                            <?php } ?>

                        </div>

                    </div>

                    <div class="small-space"></div>
                    <?php if ($show_edit) { ?>
                        <nav class="center-align">
                            <label class="switch" id="switch">
                                <input type="checkbox" id="edit_checkbox">
                                <span>
                                    <i>edit</i>
                                </span>
                            </label>
                            <button id="save_button" disabled>Save</button>
                        </nav>
                    <?php } ?>
                </form>
            </article>

            <div class="medium-space"></div>

            <!-- SOLVED SECTION -->
            <section>
                <header>
                    <div class="row">
                        <i>flag</i>
                        <h5>Solved</h5>
                    </div>
                </header>
                <div class="small-divider"></div>

                <div class="grid">
                    <?php foreach ($challenges as [$_, $challenge_name, $points, $category, $count, $solved]) {
                        if ($solved) { ?>
                            <div class="s12 m6 l4">
                                <article class="border round">
                                    <div>
                                        <h5><?= htmlspecialchars($challenge_name) ?></h5>

                                    </div>
                                    <div class="row">
                                        <a class="chip fill">
                                            <span><?= $category ?></span>
                                        </a>
                                        <a class="chip fill">
                                            <i class="fa fa-trophy" aria-hidden="true"></i>
                                            <span><?= $points ?></span>
                                        </a>
                                        <a class="chip fill">
                                            <i class="fa fa-flag" aria-hidden="true"></i>
                                            <span><?= $count ?></span>
                                        </a>
                                    </div>
                                </article>

                            </div>
                    <?php }
                    } ?>
                </div>
            </section>
        </main>

        <div class="small-space"></div>

        <!-- PASSWORD CHANGE MODAL -->
        <div class="modal" id="change_password_modal">
            <div class="row">
                <div class="max"></div>
                <h4>Change Password</h4>
                <div class="max"></div>
            </div>
            <div class="small-space"></div>

            <form id="update_password_form">
                <div class="field label border" id="update_pass_div">
                    <input type="password" id="update_pass">
                    <label for="update_pass">Password</label>
                    <span class="error invisible" id="update_pass_error_min_length">Your password is too short</span>
                </div>

                <div class="field label border" id="update_confirm_div">
                    <input type="password" id="update_confirm">
                    <label for="update_confirm">Confirm Password</label>
                    <span class="error invisible" id="update_confirm_error_confirm">Passwords do not match</span>
                </div>

                <label class="checkbox">
                    <input type="checkbox" id="update_show_password">
                    <span>Show passwords</span>
                </label>

                <div class="small-space"></div>
                <nav class="center-align">
                    <button type="button" class="border" data-ui="#change_password_modal">Cancel</button>
                    <button id="update_password_button">Edit</button>
                </nav>


            </form>
        </div>

    </body>

    </html>

<?php

} catch (Throwable $e) {
    print_exception($e);
}
