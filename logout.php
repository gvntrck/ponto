<?php
require_once('../wp-load.php');

wp_logout();

header('Location: index.php');
exit;
?>
