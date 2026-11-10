<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../connessionedb.php';

// Solo gli utenti autenticati possono prenotare un turno
if (empty($_SESSION['user_id'])) {
    http_response_code(401);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'NON_AUTENTICATO',
        'errore' => 'Devi effettuare il login prima di prenotare un turno.'
    ]);

    exit;
}

// Accetta solamente richieste POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'METODO_NON_CONSENTITO',
        'errore' => 'Metodo non consentito.'
    ]);

    exit;
}

// Legge il JSON inviato da volontariato.js
$corpo = json_decode(file_get_contents('php://input'), true);

// Recupera le fasce selezionate
$fasce =
    isset($corpo['fasce']) && is_array($corpo['fasce'])
        ? $corpo['fasce']
        : [];

// Controlla che sia stata selezionata almeno una fascia
if (empty($fasce)) {
    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'DATI_MANCANTI',
        'errore' => 'Seleziona almeno una fascia oraria.'
    ]);

    exit;
}

// Connessione al database
$conn = connetti_db('modifier', 'Str0ng#Admin9');

// ID dell'utente autenticato
$utente_id = (int) $_SESSION['user_id'];

/*
 * Nel database fascia_oraria è un DATETIME.
 * I dati già presenti utilizzano la data 2026-06-05.
 */
$data_turni = '2026-06-05';

/*
 * Usiamo le fasce senza spazi intorno al trattino.
 * Prima del confronto normalizziamo il valore ricevuto
 * dal JavaScript.
 */
$mappa_orari = [
    '09:00-11:00' => '09:00:00',
    '11:00-13:00' => '11:00:00',
    '14:00-16:00' => '14:00:00',
    '16:00-18:00' => '16:00:00'
];

$fasce_db = [];

// Converte le fasce ricevute nel formato del database
foreach ($fasce as $fascia) {

    // Elimina eventuali spazi iniziali e finali
    $fascia = trim($fascia);

    // Normalizza gli spazi intorno al trattino.
    // Esempio:
    // "14:00 - 16:00" diventa "14:00-16:00"
    $fascia = preg_replace('/\s*-\s*/', '-', $fascia);

    // Controlla che la fascia sia valida
    if (!isset($mappa_orari[$fascia])) {

        http_response_code(400);

        echo json_encode([
            'successo' => false,
            'codice_errore' => 'FASCIA_NON_VALIDA',
            'errore' => 'È stata selezionata una fascia oraria non valida.'
        ]);

        mysqli_close($conn);
        exit;
    }

    // Costruisce il DATETIME da salvare nel database
    $fasce_db[] =
        $data_turni . ' ' . $mappa_orari[$fascia];
}

/*
 * CONTROLLO DISPONIBILITÀ
 *
 * Ogni fascia può avere al massimo 2 volontari.
 */
foreach ($fasce_db as $fascia_db) {

    $stmt = mysqli_prepare(
        $conn,
        'SELECT COUNT(*)
         FROM turni_volontariato
         WHERE fascia_oraria = ?'
    );

    if (!$stmt) {

        http_response_code(500);

        echo json_encode([
            'successo' => false,
            'codice_errore' => 'ERRORE_DATABASE',
            'errore' => 'Errore durante il controllo della disponibilità.'
        ]);

        mysqli_close($conn);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        's',
        $fascia_db
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result(
        $stmt,
        $numero_volontari
    );

    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);

    // Se ci sono già due volontari la fascia è completa
    if ($numero_volontari >= 2) {

        http_response_code(409);

        echo json_encode([
            'successo' => false,
            'codice_errore' => 'FASCIA_COMPLETA',
            'errore' => 'Una delle fasce selezionate ha già raggiunto il limite di due volontari.'
        ]);

        mysqli_close($conn);
        exit;
    }
}

/*
 * INSERIMENTO DEL TURNO
 */
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO turni_volontariato
     (utente_id, fascia_oraria)
     VALUES (?, ?)'
);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        'successo' => false,
        'codice_errore' => 'ERRORE_DATABASE',
        'errore' => 'Errore durante la preparazione della prenotazione.'
    ]);

    mysqli_close($conn);
    exit;
}

// Inserisce tutte le fasce selezionate
foreach ($fasce_db as $fascia_db) {

    mysqli_stmt_bind_param(
        $stmt,
        'is',
        $utente_id,
        $fascia_db
    );

    if (!mysqli_stmt_execute($stmt)) {

        http_response_code(500);

        echo json_encode([
            'successo' => false,
            'codice_errore' => 'ERRORE_DATABASE',
            'errore' => 'Errore durante la prenotazione del turno.'
        ]);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        exit;
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Risposta positiva al JavaScript
echo json_encode([
    'successo' => true,
    'messaggio' => 'Turno prenotato correttamente!'
]);
?>