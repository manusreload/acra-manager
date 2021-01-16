<?php
require_once "core/auth.php";
require_once "core/check-user.php";
$messages = array();
error_reporting(E_ALL);
if (!haveLogin()) {
    header("Location: login.php");
} else {
    $upload_error = "";
    if (isset($_REQUEST['new_app_name'])) {
        getUser()->createApp($_REQUEST['new_app_name'], $_REQUEST['new_app_pkg']);
    } else if (isset($_REQUEST['remove'])) {
        getUser()->removeApp($_REQUEST['remove']);
    } else if (isset($_REQUEST['app'])) {
        if(isset($_REQUEST['new_app_pkg']))
        {
            
            getUser()->changeSettings($_REQUEST['app'], $_REQUEST['new_app_pkg'], $_REQUEST['new_app_pkg_errors'], $_REQUEST['push_url']);

        }
        if(isset($_REQUEST['do_notify']))
        {
            $status = 0;
            if(isset($_REQUEST['do_notify']))
                $status = 1;
            getUser()->notify($_REQUEST['app'], $status);

        }
        if(isset($_REQUEST['send_email']))
        {
            if(getUser()->sendVerificationMail())
            {
                $messages[] = "Se ha enviado un email de prueba";
            }
            else{
                $messages[] = "Error al enviar el mensaje!";
            }
        }
        $showApp = $_REQUEST['app'];
    } else if (isset($_POST['source_app'])) {
        $upload = $_FILES['source'];
        if ($upload['error'] != 0) {
            $upload_error = "Se a producido un error en la subida!";
        } else {
            if ($upload['size'] > 1024 * 1024) {
                $upload_error = "Archivo muy grande!";
            } else {
                $file = $upload['tmp_name'];
                $zip = new ZipArchive;
                $res = $zip->open($file);
                if ($res === TRUE) {
                    $zip->extractTo('unzip');
                    $zip->close();
                } else {
                    $upload_error = "No puedo abrir el ZIP!";
                }
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
        <head>
    <?php require_once "fragment/head.php"; ?>
            <script src="http://code.jquery.com/jquery-latest.js"></script>
            <script>
                function loadapp(id) {
                    ajaxLoad({
                        content: "app_info_container",
                        error: "ajax-error",
                        loading: "ajax-loading",
                        url: "ajax/GetApp.php?id=" + id
                    });
                }
            </script>
        </head>
            <?php
            if (isset($showApp)) {
                echo "<body onload=\"loadapp('$showApp')\">";
            } else {
                echo "<body>";
            }
            ?>
        <div class="modal fade" id="create_app">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Create new app</h4>
                    </div>
                    <form action="cpanel.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="new_app_name" >Nombre de la APP</label>
                                <input name="new_app_name" id="new_app_name" class="form-control" value=""/>
                            </div>
                            <div class="form-group">
                                <label for="new_app_pkg" >Nombre del Paquete</label>
                                <input name="new_app_pkg" class="form-control" id="new_app_pkg" value="" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submit()">Create</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    <?php require_once "fragment/menu.php"; ?>

        <div class="container">
            <section id="info">
                <div class="page-header">
                    <h2>Cpanel - Manage your Apps</h2>
                </div>
                <?php
                foreach ($messages as $message)
                {
                    ?>
                    <div class="alert alert-info">
                        <?= $message; ?>
                    </div>
                <?php
                }


                ?>
                <div class="row">
                    <div class="span3 well">
                        <h3>Apps</h3>
    <?php
    $arr = getUser()->getApps();

    foreach ($arr as $row) {
        echo "<blockquote><p><a href=\"#\" onclick=\"loadapp('" . $row['appId'] . "')\">" . $row['name'] . "</a></p></blockquote>";
    }
    ?>
                        <hr>
                        <button type="button" class="span2 btn btn-default" data-toggle="modal" href="#create_app">New App</button>
                    </div>
                    <div class="span8 well" id="app_info_container">

    <?php if ($upload_error != "") {
        ?>
                            <font color="red">Se a producido un error en la subida: <?php echo $upload_error ?></font>

                        <?php } else { ?>
                            Bienvenido al panel!
                        <?php } ?>
                    </div>
                </div>
                <div id="ajax-error">

                </div>
            </section>

                        <?php require_once "fragment/footer.php"; ?>

        </div>

                        <?php require_once "fragment/scripts.php"; ?>

    </body>
    </html>

    <?php
}
?>
