<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../connessionedb.php';

// Solo gli amministratori possono inserire nuovi gatti.
if (empty($_SESSION['is_admin'])) {
    http_response_code(403);

    echo json_encode([
        'successo' => false,
        'errore' => 'Operazione riservata agli amministratori.'
    ]);

    exit;
}

// L'endpoint accetta solo richieste POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'successo' => false,
        'errore' => 'Metodo non consentito.'
    ]);

    exit;
}

// Legge il JSON inviato da nuovi_gatti.js.
$gatto = json_decode(
    file_get_contents('php://input'),
    true
);

$campi = [
    'nome',
    'descrizione',
    'peso',
    'colore_mantello',
    'lunghezza_pelo',
    'razza',
    'colore_occhi',
    'eta',
    'sesso',
    'data_arrivo'
];

// Controlla che il JSON sia valido.
if (!is_array($gatto)) {
    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'errore' => 'Dati non validi.'
    ]);

    exit;
}

// Controlla che tutti i campi siano presenti.
foreach ($campi as $campo) {
    if (!isset($gatto[$campo]) || $gatto[$campo] === '') {
        http_response_code(400);

        echo json_encode([
            'successo' => false,
            'errore' => 'Tutti i campi sono obbligatori.'
        ]);

        exit;
    }
}

// Recupera e converte i dati ricevuti.
$nome = trim($gatto['nome']);
$descrizione = trim($gatto['descrizione']);
$peso = (float) $gatto['peso'];
$mantello = trim($gatto['colore_mantello']);
$pelo = $gatto['lunghezza_pelo'];
$razza = trim($gatto['razza']);
$occhi = trim($gatto['colore_occhi']);
$eta = (int) $gatto['eta'];
$sesso = $gatto['sesso'];
$data = $gatto['data_arrivo'];

// Ripete lato server i principali controlli fatti in JavaScript.
if (
    $peso <= 0 ||
    $eta < 0 ||
    !in_array($pelo, ['Corto', 'Medio', 'Lungo'], true) ||
    !in_array($sesso, ['M', 'F'], true) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) ||
    $data > date('Y-m-d')
) {
    http_response_code(400);

    echo json_encode([
        'successo' => false,
        'errore' => 'I dati inseriti non sono validi.'
    ]);

    exit;
}

// Usa l'utente modifier perché l'operazione richiede un INSERT.
$conn = connetti_db(
    'modifier',
    'Str0ng#Admin9'
);

// Prepared statement per evitare SQL injection.
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO gatti
    (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

mysqli_stmt_bind_param(
    $stmt,
    'ssdssssiss',
    $nome,
    $descrizione,
    $peso,
    $mantello,
    $pelo,
    $razza,
    $occhi,
    $eta,
    $sesso,
    $data
);

// Esegue l'inserimento e restituisce il risultato.
if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        'successo' => true
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        'successo' => false,
        'errore' => 'Errore durante l\'inserimento del gatto.'
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>