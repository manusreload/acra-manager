<!DOCTYPE html>
<?php
require_once "core/auth.php";
require_once "core/check-user.php";
if (haveLogin()) {
    if (isset($_POST['goto'])) {
        header("Location: http://" . $_POST['goto']);
    } else {
        header("Location: cpanel.php");
    }
}
?>
<html lang="es">
    <head>
        <?php require_once "fragment/head.php"; ?>
    </head>

    <body>

        <?php require_once "fragment/menu.php"; ?>

        <div class="container">

            <section id="info">
                <?php
                if (!haveLogin()) {
                    ?>
                    <div class="page-header">
                        <h2>Login to CPanel and manage yours Apps</h2>
                    </div>
                    <div class="row">
                        <div class="span8 well">
                            <?php
                            if (isset($_GET['action']) && $_GET['action'] == 'do_login') {
                                echo "<p>Contrase&ntilde;a o email incorrecto</p>";
                            }
                            ?>
                            <form action="login.php?action=do_login" method="post">
                                <fieldset>
                                    <legend>Sign in</legend>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" id="inputEmail" placeholder="Email"  name="email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                                        <div class="col-lg-10">
                                            <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="pass" >
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox"> Remember me
                                                </label>
                                            </div>
                                            <button type="submit" class="btn btn-default">Sign in</button> | 
                                            <a class="btn btn-success" href="create_account.php" >Create Account</a>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-10">
                                        <?php
                                        if (isset($_REQUEST['goto']) && !empty($_REQUEST['goto'])) {
                                            echo "<code style='display: block'>Tras el login seras redireccionado a: <a href='http://" . $_REQUEST['goto'] . "'>" . $_REQUEST['goto'] . "</a> - <a href='login.php?goto='>Cancelar</a></code>";
                                            echo '<input type="hidden" name="goto" value="' . $_REQUEST['goto'] . '" />';
                                        }
                                        ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>

                    <?php
                } else {
                    
                }
                ?>
            </section>

            <?php require_once "fragment/footer.php"; ?>

        </div>

        <?php require_once "fragment/scripts.php"; ?>

    </body>
</html>