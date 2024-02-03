<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

try {
    $self = get($_SERVER, "PHP_SELF");
    check(is_string($self));
    $page = basename($self);
    $session = Session::from_session();
    $admin = $session !== null && $session->is_admin();
?>
    <!-- ERROR TOAST -->
    <div class="toast pink white-text" id="error_toast">
        <i>error</i>
        <span id="error_toast_message">Ops, there was a problem</span>
    </div>

    <!-- NAVBAR -->
    <header>
        <nav>
            <a class="button circle transparent" href="./">
                <img class="responsive" src="images/favicon.png" alt="corns-logo">
            </a>
            <h5>CornCTF</h5>

            <div class="max"></div>

            <?php if ($session === null) { ?>
                <button class="s circle transparent" data-ui="#login_modal"><i>login</i></button>
                <button class="m l round transparent" data-ui="#login_modal">Login</button>

                <button class="s circle transparent" data-ui="#register_modal"><i>app_registration</i></button>
                <button class="m l round transparent" data-ui="#register_modal">Register</button>
            <?php } else { ?>
                <?php if ($admin) { ?>
                    <a href="admin.php" class="m button round fa-asterisk <?= $page == "admin.php" ? "" : "transparent" ?>"><i>admin_panel_settings</i></a>
                    <a href="admin.php" class="l button round <?= $page == "admin.php" ? "" : "transparent" ?>">Admin</a>
                <?php } ?>

                <a href="challenges.php" class="m circle button <?= $page == "challenges.php" ? "" : "transparent" ?>"><i>flag</i></a>
                <a href="challenges.php" class="l button round <?= $page == "challenges.php" ? "" : "transparent" ?>">Challenges</a>

                <a href="scoreboard.php" class="m circle button <?= $page == "scoreboard.php" ? "" : "transparent" ?>"><i>scoreboard</i></a>
                <a href="scoreboard.php" class="l button round <?= $page == "scoreboard.php" ? "" : "transparent" ?>">Scoreboard</a>

                <a href="show_profile.php" class="m circle button <?= $page == "show_profile.php" ? "" : "transparent" ?>"><i>account_circle</i></a>
                <a href="show_profile.php" class="l button round <?= $page == "show_profile.php" ? "" : "transparent" ?>"><?= htmlspecialchars($session->get_username()) ?></a>

                <a href="logout.php" class=" m circle button <?= $page == "logout.php" ? "" : "transparent" ?>"><i>logout</i></a>
                <a href="logout.php" class="l button round <?= $page == "logout.php" ? "" : "transparent" ?>">Logout</a>

                <button class="s circle transparent" data-ui="#pages_modal"><i>menu</i></button>
            <?php } ?>

            <button class="circle transparent" id="dark_mode"><i id="dark_mode_icon">dark_mode</i></button>



        </nav>
    </header>


    <?php if ($session === null) { ?>

        <!-- LOGIN MODAL -->
        <div class="modal" id="login_modal">
            <div class="row">
                <div class="max"></div>
                <h4>Corn Login</h4>
                <div class="max"></div>
            </div>
            <div class="small-space"></div>

            <form id="login_form">
                <div class="field label border" id="login_email_div">
                    <input type="email" id="login_email">
                    <label for="login_email">Email</label>
                    <span class="error invisible" id="login_email_error_email_format">Your email is not valid</span>
                    <span class="error invisible" id="login_email_error_email_not_exists">Email does not exist</span>
                </div>
                <div class="field label border" id="login_pass_div">
                    <input type="password" id="login_pass">
                    <label for="login_pass">Password</label>
                    <span class="error invisible" id="login_pass_error_wrong_password">Your password is wrong</span>
                    <span class="error invisible" id="login_pass_error_min_length">Your password is too short</span>
                </div>

                <label class="checkbox">
                    <input type="checkbox" id="login_show_password">
                    <span>Show password</span>
                </label>

                <div class="medium-space"></div>


                <label class="checkbox center">
                    <input type="checkbox" id="login_rememberme" checked>
                    <span>Remember me</span>
                </label>

                <div class="small-space"></div>
                <nav class="center-align">
                    <button type="button" class="border" data-ui="#login_modal">Cancel</button>
                    <button>Login</button>
                </nav>
            </form>
        </div>



        <!-- REGISTRATION MODAL -->
        <div class="modal large-width" id="register_modal">
            <div class="row">
                <div class="max"></div>
                <h4>Corn Registration</h4>
                <div class="max"></div>
            </div>
            <div class="small-space"></div>

            <form id="register_form">
                <div class="grid">
                    <div class="s6">
                        <div class="field label border" id="register_firstname_div">
                            <input type="text" id="register_firstname">
                            <label for="register_firstname">First name</label>
                            <span class="error invisible" id="register_firstname_error_min_length">Your first name is too short</span>
                            <span class="error invisible" id="register_firstname_error_max_length">Your first name is too long</span>
                        </div>
                    </div>

                    <div class="s6">
                        <div class="field label border" id="register_lastname_div">
                            <input type="text" id="register_lastname">
                            <label for="register_lastname">Last name</label>
                            <span class="error invisible" id="register_lastname_error_min_length">Your last name is too short</span>
                            <span class="error invisible" id="register_lastname_error_max_length">Your last name is too long</span>
                        </div>
                    </div>
                </div>


                <div class="field label border" id="register_username_div">
                    <input type="text" id="register_username">
                    <label for="register_username">Username</label>
                    <span class="error invisible" id="register_username_error_min_length">Your username is too short</span>
                    <span class="error invisible" id="register_username_error_max_length">Your username is too long</span>
                    <span class="error invisible" id="register_username_error_username_exists">Username already taken</span>
                </div>

                <div class="field label border" id="register_email_div">
                    <input type="email" id="register_email">
                    <label for="register_email">Email</label>
                    <span class="error invisible" id="register_email_error_email_format">Your email is not valid</span>
                    <span class="error invisible" id="register_email_error_email_exists">Email already used</span>
                </div>


                <div class="grid">
                    <div class="s6">
                        <div class="field label border" id="register_pass_div">
                            <input type="password" id="register_pass">
                            <label for="register_pass">Password</label>
                            <span class="error invisible" id="register_pass_error_min_length">Your password is too short</span>
                        </div>
                    </div>

                    <div class="s6">
                        <div class="field label border" id="register_confirm_div">
                            <input type="password" id="register_confirm">
                            <label for="register_confirm">Confirm Password</label>
                            <span class="error invisible" id="register_confirm_error_confirm">Passwords do not match</span>
                        </div>
                    </div>
                </div>

                <label class="checkbox">
                    <input type="checkbox" id="register_show_password">
                    <span>Show passwords</span>
                </label>

                <div class="medium-space"></div>

                <label class="checkbox center">
                    <input type="checkbox" id="register_rememberme" checked>
                    <span>Remember me</span>
                </label>

                <div class="small-space"></div>
                <p class="center-align">By creating an account, you agree to CornCtf's <a class="primary-text" href="#"> privacy policy</a></p>

                <div class="small-space"></div>
                <nav class="center-align">
                    <button type="button" class="border" data-ui="#register_modal">Cancel</button>
                    <button>Register</button>
                </nav>
            </form>
        </div>
    <?php } else { ?>
        <!-- PAGES MODALE -->
        <div class="right modal" id="pages_modal">
            <div class="row">
                <div class="max"></div>
                <h5>CornMenù</h5>
                <div class="max"></div>
            </div>
            <?php if ($admin) { ?>
                <a href="admin.php" class="row round ">
                    <i>admin_panel_settings</i>
                    <span>Admin</span>
                </a>
            <?php } ?>

            <a href="challenges.php" class="row round ">
                <i>flag</i>
                <span>Challenges</span>
            </a>
            <a href="scoreboard.php" class="row round ">
                <i>scoreboard</i>
                <span>Scoreboard</span>
            </a>
            <a href="show_profile.php" class="row round ">
                <i>account_circle</i>
                <span><?= htmlspecialchars($session->get_username()) ?></span>
            </a>
            <a href="logout.php" class="row round ">
                <i>logout</i>
                <span>Logout</span>
            </a>
            <div class="medium-space"></div>
            <nav class="right-align">
                <button class="border" data-ui="#pages_modal">Cancel</button>
            </nav>

        </div>


    <?php } ?>


<?php
} catch (Throwable $e) {
    print_exception($e);
    return;
}
