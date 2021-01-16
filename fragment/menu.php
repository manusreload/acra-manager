

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/index.php">Report Manager</a>
        <div class="nav-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="#about" data-toggle="modal">Acerca</a></li>
                <li><a href="help.php">Help</a></li>

            </ul>

        </div><!--/.nav-collapse -->
        <?php
        if (function_exists("haveLogin") && haveLogin()) {
            ?>
            <div class="nav-collapse pull-right">
                <ul class="nav navbar-nav">
                    <li><a href="index.php?action=do_logout">Logout</a></li>
                </ul>
            </div>
            <?php
        } else {
            ?>
            <form class="navbar-form form-inline pull-right">
                <button type="submit" class="btn">Sign in</button>
            </form>
            <?php
        }
        ?>

    </div>
</div>


<!-- Modal -->

<div class="modal fade" id="about">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Acerca de PTA (Push To All)</h4>
            </div>
            <div class="modal-body">
                <p>Push To All tiene como fin poder recibir notificaciones push para cualquier lenguaje. Desde JavaScript hasta C++.</p>
                <p>Se basa en el uso de un servidor WebSocket en el cual se "logean" los clientes y esperan a los mensajes enviados por tu Aplicacion.</p>
                <p>PTA ha sido creado por Manus Reload (<a href="mailto:manus.reload@gmail.com">manus.reload@gmail.com</a>)</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal -->
<div id="contact" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Contactar</h3>
    </div>
    <div class="modal-body">
        <p>Puedes contactarnos en:
        <address>
            <strong>Email:</strong> <a href="mailto:#">lo_que_sea@gmail.com</a>
        </address>
        </p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
    </div>
</div>
