<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');

?>
<!DOCTYPE html>
<html>
    <?php include('includes/main/head.php'); ?>
    <body>
        <div id="page-container">
            <?php include('includes/main/nav.php'); ?>
            <div id="container">
                <?= $CONTENT_DATA ?>
            </div>
            <?php include('includes/main/footer.php'); ?>
        </div>
    </body>
</html>

