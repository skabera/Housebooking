<?php
session_start();
session_destroy();
header("Location: /booking/index.php");
exit();
?>
