<?php
// Avvia la sessione solo se non è già attiva.
// La sessione serve per mantenere i dati dell'utente autenticato durante la navigazione.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Funzione comune per la connessione al database.
// Riceve username e password dell'utente MySQL da utilizzare, permettendo alle diverse pagine
// di collegarsi con privilegi differenti (lecture, modifier o registrator).
function connetti_db($utente_db, $password_db) {
    $conn = mysqli_connect("localhost", $utente_db, $password_db, "gattile_db");

    // Se la connessione è avvenuta correttamente, imposta UTF-8
    // per gestire caratteri accentati e caratteri speciali.
    if ($conn) {
        mysqli_set_charset($conn, "utf8mb4");
    }

    return $conn;
}

// Calcola l'hash della password secondo il formato utilizzato nel database del progetto.
// L'asterisco iniziale permette di distinguere le password memorizzate tramite hash
// dagli eventuali account precaricati con password in chiaro.
function calcola_hash_password($password) {
    return '*' . sha1(sha1($password));
}

// Verifica la password inserita durante il login confrontandola con quella salvata nel database.
function verifica_password($password, $psw_db) {

    // Se la password salvata inizia con '*', confronta il valore con l'hash calcolato.
    if (isset($psw_db[0]) && $psw_db[0] === '*') {
        return $psw_db === calcola_hash_password($password);
    }

    // Altrimenti effettua il confronto diretto per mantenere la compatibilità
    // con gli eventuali account precaricati nel database con password in chiaro.
    return $psw_db === $password;
}
?>