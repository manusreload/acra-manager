<?php
require_once "core/auth.php";
require_once "core/check-user.php";

if (!haveLogin()) {
    header("Location: login.php");
} else {
    if (isset($_REQUEST['app']) && isset($_REQUEST['vercode'])) {
        
        
        $app = getUser()->getApp($_REQUEST['app']);
?>
        <!DOCTYPE html>
        <html lang = "es">
        <head>
        <?php require_once "fragment/head.php";
        ?>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>
            function loadapp(id,vercode) {
                ajaxLoad({
                    content : "app_error_container",
                    error : "ajax-error",
                    loading: "ajax-loading",
                    url: "ajax/GetError.php?id=" + id + "&vercode=" + vercode + "&page=<?php echo $_REQUEST['page']; ?>&order=<?php echo $_REQUEST['order']; ?>"
                });
                $("#search").keyup(function(event){
                    if(event.keyCode == 13){
                        search($("#search").val())
                    }
                });
            }
            function search(text) {
                ajaxLoad({
                    content : "app_error_container",
                    error : "ajax-error",
                    loading: "ajax-loading",
                    url: "ajax/Search.php?id=<?php echo $_REQUEST['app']; ?>&vercode=<?php echo $_REQUEST['vercode']; ?>&q=" + text
                });
            }
            
        </script>
        </head>

        <body onload="loadapp('<?php echo $_REQUEST['app'] . "','" . $_REQUEST['vercode']; ?>')">
            
            <?php require_once "fragment/menu.php"; ?>

            <div class="container">
                <section id="info">
                    <div class="page-header">
                        <h2>App errors <small> - <a href="cpanel.php">Atras</a></small></h2>
                    </div>
                    <div class="row well">
                        <div class="input-append">
                            <input id="search" class="input-xlarge form-control" placeholder="Buscar..." />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 well">
                            <h3>Versions</h3>
                            <?php
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
                            ?>
                        </div>
                        <div class="col-lg-10" id="app_error_container">
                            Loading...
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
else
{
header("Location: cpanel.php");
}
}
?>
