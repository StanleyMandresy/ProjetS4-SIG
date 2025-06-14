<?php
$host = "localhost";
$dbname = "marche_sig";
$user = "postgres";
$password = "stan";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
