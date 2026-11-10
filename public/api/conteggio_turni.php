<?php
// Restituisce il numero di volontari già prenotati
// per ogni fascia oraria in formato JSON.

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../connessionedb.php';

// Questa API deve solamente leggere i dati,
// quindi utilizza l'utente MySQL "lecture".
$conn = connetti_db('lecture', 'P@ssw0rd!');

// Accetta solamente richieste GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    http_response_code(405);

    echo json_encode([
        'successo' => false,
        'errore' => 'Metodo non consentito.'
    ]);

    exit;
}

// Conta quanti volontari sono già presenti
// per ciascuna fascia oraria.
$sql = '
    SELECT fascia_oraria, COUNT(*) AS numero_volontari
    FROM turni_volontariato
    GROUP BY fascia_oraria
';

$risultato = mysqli_query($conn, $sql);

// Controlla eventuali errori nella query.
if (!$risultato) {

    http_response_code(500);

    echo json_encode([
        'successo' => false,
        'errore' => 'Errore durante il recupero dei turni.'
    ]);

    mysqli_close($conn);
    exit;
}

$turni = [];

// Associa gli orari presenti nel database
// alle fasce utilizzate nel form.
while ($riga = mysqli_fetch_assoc($risultato)) {

    // Nel database abbiamo valori del tipo:
    // 2026-06-05 09:00:00
    //
    // Recuperiamo solamente l'ora.
    $timestamp = strtotime($riga['fascia_oraria']);

    $ora = date('H:i', $timestamp);

    // Trasforma l'ora nella stessa stringa
    // utilizzata come value delle checkbox.
    switch ($ora) {

        case '09:00':
            $fascia = '09:00 - 11:00';
            break;

        case '11:00':
            $fascia = '11:00 - 13:00';
            break;

        case '14:00':
            $fascia = '14:00 - 16:00';
            break;

        case '16:00':
            $fascia = '16:00 - 18:00';
            break;

        default:
            // Se nel database è presente un orario
            // non previsto dal form, lo ignoriamo.
            continue 2;
    }

    // Salva il numero di volontari presenti
    // per quella fascia.
    $turni[$fascia] =
        ($turni[$fascia] ?? 0)
        + (int) $riga['numero_volontari'];
}

mysqli_close($conn);

// Il JavaScript utilizzerà questi conteggi
// per disabilitare le fasce che hanno già
// raggiunto il limite massimo di 2 volontari.
echo json_encode([
    'successo' => true,
    'turni' => $turni
]);
?>