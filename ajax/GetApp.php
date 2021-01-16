<?php
require_once "../core/auth.php";

function bytesToSize($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

if (haveLogin()) {
    if (isset($_GET['id'])) {
        $app = getUser()->getApp($_GET['id']);
        ?>

        <div class="modal fade" id="settings">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Settings</h4>
                    </div>
                    <form action="cpanel.php" method="post"  role="form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="new_app_pkg" >Nombre del package</label>
                                <input name="new_app_pkg" id="new_app_pkg" class="form-control" value="<?php echo $app->pkg; ?>"/>
                            </div>
                            <div class="form-group">
                                <label for="new_app_pkg_errors" >Buscar errores como</label>
                                 <input name="new_app_pkg_errors" class="form-control" id="new_app_pkg_errors" value="<?php echo $app->error_pkg; ?>" />
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="push_url" >Notificar errores al email</label>
                                 <label><input type="checkbox" name="do_notify" value="1" <?php echo ($app->getNotify()=="1")?"checked":""; ?> /> Enviar notificaciones de nuevos errores a: <u><?php echo getUser()->email; ?></u></label>
                                <p><button name="send_email" type="submit" class="btn btn-primary">Enviar Email de Prueba</button> </p>
<!--                                    <input type="hidden" name="do_notify" value="1" />-->

                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="push_url" >URL Push</label>
                                <input name="push_url" class="form-control" id="push_url" value="<?php echo $app->push; ?>" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submit()">Save</button>
                        </div>
                        <input type="hidden" name="app" value="<?php echo $_REQUEST['id']; ?>" />
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" id="remove_app">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Eliminar app</h4>
                    </div>
                    <form action="cpanel.php" method="post">
                        <div class="modal-body">
                            ¿Realmente deseas eliminar <b><?php echo $app->getName(); ?></b>?
                            <input type="hidden" name="remove" value="<?php echo $app->getAppId(); ?>" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="submit()">Eliminar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <h1><?php echo $app->getName(); ?></h1>
        <br>
        <blockquote>
            <h2>Aplication Key</h2>
            <code><?php echo $app->getAppId(); ?></code>
            <small class="help-block">Utilize esta id para enviar nuevos reportes.</small>
        </blockquote>
        <blockquote>

            <h2>Adjuntar Codigo Fuente</h2>
            <form method="POST" action="cpanel.php" enctype="multipart/form-data">
                <input type="file" name="source" />
                <input type="hidden" name="source_app" value="<?php echo $app->getAppId(); ?>" />
                <span class="help-block">Archivos zip menores de 1M con el AndroidManifest.xml en su raiz.</span><input type="submit" value="Subir"/>
            </form>

        </blockquote>
        <blockquote>
            <h3>Versiones</h3>
            <div>
                <?php
                try
                {

                    $ret = ($app->listVersions($app->pkg));
                    if(count($ret) > 0)
                    {
                        echo "<ul>";
                        foreach ($ret as $version) {
                            echo "<li><a href=\"errors.php?app={$app->getAppId()}&vercode={$version['vercode']}\">Version {$version['version']}</a> <span class=\"badge\">{$version['count']}</span></li>";
                        }

                        echo "</ul>";
                    }
                    else
                    {
                        echo "No hay ningun error!";
                    }
                }
                catch(Exception $ex)
                {
                    echo "Error";
                    print_r($ex);
                }

                ?>
            </div>
        </blockquote>
        <br>
        <div class="modal-footer">
            <button data-toggle="modal" href="#settings" type="button" class="btn btn-default">Ajustes</button>
            <button data-toggle="modal" href="#remove_app" type="button" class="btn btn-danger">Eliminar</button>
        </div>
        <?php
    } else {
        echo "No se ha encontrado el id!";
    }
} else {
    echo "Por favor, logeese de nuevo <a href='login.php'>Login</a>";
}
?>
