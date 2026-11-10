<?php
// Avvia la sessione se non è già attiva.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Elimina tutte le variabili salvate nella sessione.
$_SESSION = [];

// Elimina anche il cookie associato alla sessione.
if (ini_get('session.use_cookies')) {
    $parametri = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 3600,
        $parametri['path'],
        $parametri['domain'],
        $parametri['secure'],
        $parametri['httponly']
    );
}

// Distrugge definitivamente la sessione.
session_destroy();

// Dopo il logout riporta l'utente alla home.
header('Location: home.php');
exit;