<?php
require_once "core/auth.php";
require_once "core/check-user.php";

if (haveLogin()) {
    header("Location: cpanel.php");
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <?php require_once "fragment/head.php"; ?>
        </head>

        <body>

            <?php require_once "fragment/menu.php"; ?>

            <div class="container">

                Bienvenido
                <p>
                    <a href="login.php">Logearse<a/>

                </p>

                <?php require_once "fragment/footer.php"; ?>

            </div>

            <?php require_once "fragment/scripts.php"; ?>

        </body>
    </html>

    <?php
}
?>