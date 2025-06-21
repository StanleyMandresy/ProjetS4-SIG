<?php
session_start();
require 'config/config.php';

$error = $_GET['error'] ?? null;

// Si déjà connecté
if (isset($_SESSION['pseudo'])) {
    header("Location: pages/carte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
</head>
<body>
    <h1>Connexion</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="api/login.php">
        <label>Pseudo :</label><br>
        <input type="text" name="username" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
