<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

Session::require_admin();

$db = new DB();

$challenges = list_challenges($db);

try {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php require_once(__DIR__ . "/templates/head.php"); ?>
        <script src="js/admin.js" type="module"></script>
    </head>

    <body>
        <?php require_once(__DIR__ . "/templates/navbar.php"); ?>
        <main>
            <table class="border" id="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Points</th>
                        <th>Category</th>
                        <th>Solved</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($challenges as [$id, $name, $points, $category, $solved]) { ?>
                        <tr id="<?= $id ?>">
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><?= $points ?></td>
                            <td><?= htmlspecialchars($category) ?></td>
                            <td><?= $solved ?></td>
                            <td><button class="round" id="edit_<?= $id ?>"><i>build</i>Edit</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>

        <div class="modal large-width" id="modal">
            <div class="row">
                <div class="max"></div>
                <h5>Edit challenge</h5>
                <div class="max"></div>
            </div>
            <div class="small-space"></div>

            <form id="form">
                <div class="field label border" id="name_div">
                    <input type="text" id="name">
                    <label for="name">Name</label>
                    <span class="error invisible" id="name_error_min_length">The challenge name is too short</span>
                    <span class="error invisible" id="name_error_max_length">The challenge name is too long</span>
                </div>

                <div class="field label border" id="points_div">
                    <input type="int" id="points">
                    <label for="points">Points</label>
                    <span class="error invisible" id="points_error_points_format">This is not a valid value</span>
                    <span class="error invisible" id="points_error_min_value">Points are too low</span>
                    <span class="error invisible" id="points_error_max_value">Points are too high</span>
                </div>

                <div class="field label border" id="category_div">
                    <input type="text" id="category">
                    <label for="category">Category</label>
                    <span class="error invisible" id="category_error_min_length">The challenge category is too short</span>
                    <span class="error invisible" id="category_error_max_length">The challenge category is too long</span>
                </div>

                <div class="field label border" id="flag_div">
                    <input type="text" id="flag">
                    <label for="flag">Flag</label>
                    <span class="error invisible" id="flag_error_min_length">The challenge flag is too short</span>
                    <span class="error invisible" id="flag_error_max_length">The challenge flag is too long</span>
                </div>

                <div class="field textarea label border extra" id="description_div">
                    <textarea id="description"></textarea>
                    <label for="description">Description</label>
                    <span class="error invisible" id="description_error_max_length">The challenge description is too long</span>
                </div>

                <div id="attachments">
                    <div id="template">
                        <button type="button" class="button" id="add_attachment"><i>Add</i></button>
                        <input type="file" id="attachment" hidden multiple>
                        <div class="small-space"></div>
                    </div>
                </div>

                <nav class="center-align">
                    <button id="delete" class="red" type="button">Delete</button>
                    <button type="button" class="border" data-ui="#modal">Cancel</button>
                    <button id="submit">Confirm</button>
                </nav>
            </form>
        </div>

        <div class="fixed bottom right large-padding">
            <button class="square round extra" id="add">
                <i>add</i>
            </button>
        </div>
    </body>


    </html>

<?php
} catch (Throwable $e) {
    print_exception($e);
}
