<?php
    $version = isset($_GET["version"]) ? intval($_GET["version"]) : die("version");
    file_put_contents("version.txt", $version);
?>