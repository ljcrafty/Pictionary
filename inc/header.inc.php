<?php
    require_once "/home/MAIN/lxj7261/Sites/442/project/inc/consts.inc.php";
    require_once HELPERS . "Lib.php";

    sessionStart();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css?family=Dosis:400,800" rel="stylesheet">
    <title>!Pictionary | <?= $title ?></title>
    <script src="<?= JS ?>jquery-3.3.1.min.js"></script>
    <script src="<?= JS ?>jquery-ui.min.js"></script>
    <script src="<?= JS ?>sjcl.js"></script>
    <script src="<?= JS ?>bitArray.js"></script>
    <script src="<?= JS ?>cbc.js"></script>
    <script src="<?= JS ?>jsencrypt.min.js"></script>
    <script src="<?= JS ?>functions.js"></script>
</head>