<?php
// Avvia la sessione per sapere se l'utente è autenticato.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Include l'header comune del sito.
// L'header apre anche il tag <main>.
require __DIR__ . '/../includes/header.php';
?>

<section id="sezione-volontariato">

    <h1>Turni di volontariato</h1>

    <p>
        Seleziona una o più fasce orarie in cui vuoi aiutare il gattile.
        Ogni fascia può accogliere al massimo due volontari.
    </p>

    <!-- Qui JavaScript mostrerà i messaggi -->
    <p id="messaggio_volontariato" role="status"></p>

    <form id="volontariato" novalidate>

        <fieldset>

            <legend>Fasce disponibili</legend>

            <label>
                <input
                    type="checkbox"
                    name="fasce[]"
                    value="09:00-11:00"
                >
                09:00 - 11:00
            </label>

            <label>
                <input
                    type="checkbox"
                    name="fasce[]"
                    value="11:00-13:00"
                >
                11:00 - 13:00
            </label>

            <label>
                <input
                    type="checkbox"
                    name="fasce[]"
                    value="14:00-16:00"
                >
                14:00 - 16:00
            </label>

            <label>
                <input
                    type="checkbox"
                    name="fasce[]"
                    value="16:00-18:00"
                >
                16:00 - 18:00
            </label>

        </fieldset>

        <button
            type="submit"
            id="btn_prenota_turno"
        >
            Prenota turno
        </button>

    </form>

</section>

<!-- Comunica a volontariato.js se l'utente è autenticato -->
<script>
    const utenteAutenticato =
        <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>

<!-- Vanilla JavaScript per disponibilità e prenotazione turni -->
<script src="../scripts/volontariato.js"></script>

<?php
// Include il footer comune.
// Il footer chiude anche il tag </main>.
require __DIR__ . '/../includes/footer.php';
?>