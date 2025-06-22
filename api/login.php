<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE pseudo = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password==$user['motdepasse']) {
        $_SESSION['pseudo'] = $user['pseudo'];
        header("Location: ../pages/carte.php");
        exit;
    } else {
        header("Location: ../index.php?error=Identifiants incorrects");
        exit;
    }
}
print_r(error_reporting(E_ALL));
print_r(ini_get('display_errors'));
print_r(ini_get('display_startup_errors'));
?>      

