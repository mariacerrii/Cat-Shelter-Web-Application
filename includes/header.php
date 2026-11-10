<?php
// Avvia la sessione se non è già attiva.
// La sessione permette all'header di sapere se l'utente è loggato o amministratore.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gattile del Lago d'Orta</title>

    <!-- Foglio di stile principale del sito -->
    <link rel="stylesheet" href="../style.css">
</head>

<body>
<header>
    <!-- Logo del Gattile del Lago d'Orta -->
    <a href="home.php" class="logo-link">
        <img
            src="assets/img/logo.png"
            alt="Gattile del Lago d'Orta - Una casa per tutti"
            class="logo-gattile"
        >
    </a>
</header>

<!-- Menu di navigazione comune a tutte le pagine -->
<nav class="nav_container">
    <div class="nav_links">
        <a href="home.php">Home</a>
        <?php if (empty($_SESSION['user_id'])): ?>

            <!-- Link mostrati solo agli utenti non autenticati -->
            <a href="signin.php">Login</a>
            <a href="registrazione.php">Registrazione</a>
        <?php else: ?>
            <!-- Logout mostrato solo agli utenti autenticati -->
            <a href="logout.php">Logout</a>
        <?php endif; ?>
        <a href="turni_volontariato.php">Volontariato</a>
        <?php if (!empty($_SESSION['is_admin'])): ?>

            <!-- Voce mostrata esclusivamente agli amministratori -->
            <a href="nuovi_gatti.php">Gestione gatti</a>

        <?php endif; ?>

    </div>

    <!-- Username dell'utente autenticato -->
    <div class="nav_user">

        <?php if (!empty($_SESSION['username'])): ?>

            Benvenuto
            <strong>
                <?= htmlspecialchars($_SESSION['username']) ?>
            </strong>

        <?php else: ?>

            <em>Non loggato</em>

        <?php endif; ?>

    </div>

</nav>

<main>