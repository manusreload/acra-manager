<!DOCTYPE html>
<html lang="es">
    <head>
        <?php require_once "fragment/head.php"; ?>
    </head>

    <body>

        <?php require_once "fragment/menu.php"; ?>

        <div class="container">

            <section id="info">
                <?php
                if (!isset($_POST["create_account"])) {
                    ?>
                    <div class="page-header">
                        <h2>Crear una cuenta nueva</h2>
                    </div>
                    <div class="row">
                        <div class="span6 well">
                            <h3>Crear una cuenta en Cloud Device Manager</h3>
                            <form action="create_account.php" method="post">
                                <label for="email">Email: </label><input class="form-control" type="text" name="email" />
                                <label for="pass">Contrase&ntilde;a: </label><input class="form-control" type="password" name="pass" />
                                <label for="name">Nombre y Apellidos: </label><input class="form-control" type="text" name="name" />
                                <p>
                                    <br>
                                    <input class="btn btn-large btn-primary" type="submit" value="Crear Cuenta" name="create_account" />
                                </p>
                            </form>
                        </div>
                    </div>

                    <?php
                } else {
                    require_once 'class/CDMCore.php';

                    $core = new CDMCore();
                    $email = $_POST['email'];
                    $pass = $_POST['pass'];
                    $name = $_POST['name'];
                    $res = $core->registerUser($email, $pass, $name);

                    if ($res == -2) {
                        echo "<h1>Opss...</h1>";
                        echo "Parece que ha ocurrido un error, quias ese correo ya este registrado...";
                    } else if ($res == -1) {
                        echo "<h1>Opss...</h1>";
                        echo "Parece que ha ocurrido un error enviandote el email...";
                    } else {
                        echo "<h1>Vamos por buen camino!</h1>";
                        echo "Revise su bandeja de entrada, ¡Le hemos enviado un email!";
                    }
                }
                ?>
            </section>

            <?php require_once "fragment/footer.php"; ?>

        </div>

        <?php require_once "fragment/scripts.php"; ?>

    </body>
</html>