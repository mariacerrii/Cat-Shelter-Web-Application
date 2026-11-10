<?php
// Restituisce una risposta JSON
header('Content-Type: application/json; charset=utf-8');

// Avvia la sessione per verificare che l'utente sia autenticato
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include il file comune per la connessione al database.
require_once __DIR__ . '/../../connessionedb.php';

// Usa l'utente MySQL "modifier", che possiede privilegi di lettura e scrittura,
// perché questa pagina deve inserire prenotazioni e associare i gatti selezionati.
$conn = connetti_db("modifier", "Str0ng#Admin9");

// Controlla il metodo HTTP utilizzato per effettuare la richiesta al server
// $_SERVER['REQUEST_METHOD'] --> contiene il metodo della richiesta 
// Poiché prenotazione.js invia i dati tramite fetch() con metodo POST, il backend accetta solo POST
// Se viene utilizzato un metodo diverso, restituisce una risposta JSON di errore e con exit interrompe l'esecuzione dello script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'successo' => false,
        'codice_errore' => 'METODO_NON_CONSENTITO',
        'errore' => 'Metodo di richiesta non consentito.'
    ]);
    exit;
}
// Se l'utente non è autenticato blocca la prenotazione
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'successo' => false,
        'codice_errore' => 'NON_AUTENTICATO',
        'errore' => 'Devi effettuare il login o registrarti prima di prenotare una visita!'
    ]);
    exit;
}

// Legge il JSON inviato da prenotazione.js
$corpo = json_decode(file_get_contents('php://input'), true);
$data_ora = isset($corpo['data_ora']) ? trim($corpo['data_ora']) : '';
$gatti_selezionati = isset($corpo['gatti']) && is_array($corpo['gatti'])
    ? $corpo['gatti']
    : [];

// Verifica che siano presenti data/ora e almeno un gatto
if (empty($data_ora) || empty($gatti_selezionati)) {
    echo json_encode([
        'successo' => false,
        'codice_errore' => 'DATI_MANCANTI',
        'errore' => 'Seleziona almeno un gatto e indica data e ora della visita.'
    ]);
    exit;
}

// Impedisce lato server la prenotazione di visite nel passato
$data_visita = DateTime::createFromFormat('Y-m-d H:i:s', $data_ora);

if ($data_visita === false || $data_visita <= new DateTime()) {
    echo json_encode([
        'successo' => false,
        'codice_errore' => 'DATA_NON_VALIDA',
        'errore' => 'La data della visita deve essere valida e futura.'
    ]);
    exit;
}

// Inserisce la prenotazione usando un prepared statement per evitare SQL injection
$utente_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    'INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES (?, ?)'
);

$stmt->bind_param('is', $utente_id, $data_ora);
$stmt->execute();

// Recupera l'ID della prenotazione appena creata
$prenotazione_id = $conn->insert_id;

// Associa alla prenotazione tutti i gatti selezionati
$stmt_gatto = $conn->prepare(
    'INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES (?, ?)'
);

foreach ($gatti_selezionati as $gatto_id) {
    $gatto_id = (int) $gatto_id;

    if ($gatto_id <= 0) {
        continue;
    }

    $stmt_gatto->bind_param('ii', $prenotazione_id, $gatto_id);
    $stmt_gatto->execute();
}

// Chiude statement e connessione
$stmt->close();
$stmt_gatto->close();
$conn->close();

// Comunica al JavaScript che la prenotazione è avvenuta correttamente
echo json_encode([
    'successo' => true,
    'messaggio' => 'Visita prenotata con successo!'
]);
?>