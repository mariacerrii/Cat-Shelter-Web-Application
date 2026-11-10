<?php
// Imposta l'header per restituire una risposta JSON UTF-8
header('Content-Type: application/json; charset=utf-8');

// HEADER PERSONALIZZATI PER SICUREZZA E CACHE
// Evita che il browser memorizzi la risposta troppo a lungo
header('Cache-Control: no-cache, no-store, must-revalidate'); // Nessuna cache sul client
header('Pragma: no-cache'); // Compatibilità HTTP/1.0
header('Expires: 0'); // Data di scadenza passata

// Permette richieste CORS da qualsiasi origine (utile se React è servito separatamente)
header('Access-Control-Allow-Origin: *');

// Include il file comune per la connessione al database.
require_once __DIR__ . '/../../connessionedb.php';

// Usa l'utente MySQL "lecture", che possiede solo privilegi di lettura (SELECT),
// perché questa API deve solamente recuperare i dati dei gatti.
$conn = connetti_db("lecture", "P@ssw0rd!");

// Definisce i campi consentiti per l'ordinamento
$campi_ordinabili = ['eta', 'data_arrivo', 'razza', 'colore_mantello'];

// Il parametro limit è opzionale: se non viene specificato, l'API restituisce tutti i gatti.
// Se viene passato (ad esempio ?limit=2), limita il numero di risultati restituiti.
$limit = null;

if (isset($_GET['limit'])) {
    $limit_in = intval($_GET['limit']);

    if ($limit_in > 0) {
        $limit = $limit_in;
    }
}

// Imposta come ordinamento predefinito la data di arrivo.
// In questo modo, con DESC, vengono mostrati prima i gatti arrivati più recentemente.
$sort = 'data_arrivo';
if (isset($_GET['sort'])) {
    $sort_in = $_GET['sort'];
    if (in_array($sort_in, $campi_ordinabili, true)) {
        $sort = $sort_in;
    }
}

// Prepara la query SQL selezionando tutti i campi necessari al componente React.
// La descrizione è necessaria perché GattiList.jsx permette la ricerca per nome o descrizione.
$sql = "SELECT id, nome, descrizione, eta, colore_mantello, data_arrivo
        FROM gatti
        ORDER BY $sort DESC";

// Aggiunge LIMIT solamente quando il parametro è stato specificato nella richiesta
if ($limit !== null) {
    $sql .= " LIMIT ?";
}
// Prepara lo statement
$stmt = $conn->prepare($sql);
// Collega il parametro solo quando la query contiene LIMIT ?
if ($limit !== null) {
    $stmt->bind_param('i', $limit);
}

// Esegue la query
$stmt->execute();
// Ottiene il risultato
$result = $stmt->get_result();
// Prepara un array per i dati dei gatti
$gatti = [];
while ($row = $result->fetch_assoc()) {
    // Aggiunge a ogni gatto il percorso dell'immagine placeholder locale.
    // La proprietà "immagine" verrà letta da GattiList.jsx
    // per visualizzare la foto nella card.
    $row['immagine'] = 'assets/img/placeholdercat.png';
    $gatti[] = $row;
}

// Restituisce i dati in formato JSON
echo json_encode(
    $gatti,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

// Chiude statement e connessione
$stmt->close();
$conn->close();
?>