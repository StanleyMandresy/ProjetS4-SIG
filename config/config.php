<?php
$host = "localhost";
$dbname = "marche_SIG";
$user = "postgres";
$password = "mot_de_passe";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
