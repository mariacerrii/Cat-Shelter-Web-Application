<?php
// Restituisce tutte le risposte dell'endpoint in formato JSON UTF-8.
header('Content-Type: application/json; charset=utf-8');

// Include il file comune per la connessione al database.
require_once __DIR__ . '/../../connessionedb.php';

// Utilizza l'utente MySQL "registrator", che deve avere soltanto
// il privilegio necessario per inserire nuovi utenti nella tabella utenti.
$conn = connetti_db("registrator", "ToB31nsert?");

// L'endpoint accetta esclusivamente richieste POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'METODO_NON_CONSENTITO',
        'errore' => 'Metodo di richiesta non consentito.'
    ]);

    exit;
}

// Legge il corpo JSON inviato da registrazione.js
// e lo converte in un array PHP.
$corpo = json_decode(
    file_get_contents('php://input'),
    true
);

// Verifica che il JSON ricevuto sia valido.
if (!is_array($corpo)) {
    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'JSON_NON_VALIDO',
        'errore' => 'I dati ricevuti non sono validi.'
    ]);

    exit;
}

// Elenco dei campi obbligatori.
$campi_richiesti = [
    'nome',
    'cognome',
    'indirizzo',
    'username',
    'password'
];

// Controlla che tutti i campi siano presenti e non vuoti.
foreach ($campi_richiesti as $campo) {

    if (
        !isset($corpo[$campo]) ||
        trim((string) $corpo[$campo]) === ''
    ) {

        http_response_code(400);

        echo json_encode([
            'successo' => false,
            'codice_errore' => 'DATI_MANCANTI',
            'errore' => 'Tutti i campi sono obbligatori.'
        ]);

        exit;
    }
}

// Rimuove eventuali spazi iniziali e finali dai dati testuali.
$nome = trim($corpo['nome']);
$cognome = trim($corpo['cognome']);
$indirizzo = trim($corpo['indirizzo']);
$username = trim($corpo['username']);
$password = $corpo['password'];

// Lo username deve iniziare con una lettera
// e può contenere lettere, numeri e underscore.
if (!preg_match(
    '/^[A-Za-z][A-Za-z0-9_]*$/',
    $username
)) {

    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'USERNAME_NON_VALIDO',
        'errore' => 'Lo username deve iniziare con una lettera e può contenere solo lettere, numeri e underscore.'
    ]);

    exit;
}

// Ripete lato server tutti i controlli sulla password.
// La validazione JavaScript può infatti essere aggirata.
$password_valida =
    preg_match('/^.{8,16}$/', $password) &&
    preg_match('/[A-Z]/', $password) &&
    preg_match('/[a-z]/', $password) &&
    preg_match('/[0-9]/', $password) &&
    preg_match('/[^A-Za-z0-9]/', $password);

if (!$password_valida) {

    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'PASSWORD_NON_VALIDA',
        'errore' => 'La password deve avere 8-16 caratteri e contenere almeno una maiuscola, una minuscola, un numero e un carattere speciale.'
    ]);

    exit;
}

// Calcola l'hash della password tramite la funzione
// definita nel file comune connessionedb.php.
$password_hash = calcola_hash_password($password);

// Prepara la query di inserimento evitando di concatenare
// direttamente i dati dell'utente nella stringa SQL.
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO utenti
    (nome, cognome, indirizzo, username, password, is_admin)
    VALUES (?, ?, ?, ?, ?, FALSE)'
);

// Gestisce un eventuale errore nella preparazione della query.
if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'ERRORE_DATABASE',
        'errore' => 'Errore durante la preparazione della registrazione.'
    ]);

    exit;
}

// Collega i valori ai parametri della query preparata.
mysqli_stmt_bind_param(
    $stmt,
    'sssss',
    $nome,
    $cognome,
    $indirizzo,
    $username,
    $password_hash
);

// Esegue l'inserimento.
if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        'successo' => true,
        'messaggio' => 'Registrazione completata con successo.'
    ]);

} else {

    // Il caso più probabile è uno username già presente.
    http_response_code(409);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'USERNAME_DUPLICATO',
        'errore' => 'Username già in uso o dati non validi.'
    ]);
}

// Chiude statement e connessione.
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>