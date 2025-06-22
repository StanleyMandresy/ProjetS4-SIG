<?php
require_once('../config/config.php');
require_once('../inc/fonction.php');

$type = $_GET['type'] ?? null;
$zones = [];

if ($type) {
    $zones = getZones($type); // fonction vue précédemment
}

echo json_encode($zones);
?>
