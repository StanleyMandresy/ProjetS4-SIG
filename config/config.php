<?php
$host = "localhost";
$dbname = "marche_sig";
$user = "abc";
$password = "abc";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
