<?php
// Avvio della sessione per gestire l'utente autenticato durante la navigazione
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Variabili generali della pagina
$SITE_NAME = "Gattile del Lago d'Orta";
$NUM_LATEST_CATS = 2;

// Header comune a tutte le pagine del sito.
// L'header apre anche il tag <main>.
include __DIR__ . '/../includes/header.php';
?>


<!-- Presentazione principale del gattile -->
<section id="presentation" class="hero">

    <div class="hero-testo">

        <p class="sottotitolo">
            <?php echo htmlspecialchars($SITE_NAME); ?>
        </p>

        <h1>Una casa per tutti</h1>

        <p>
            Benvenuti al Gattile del Lago d'Orta &#128049; &#127754;<br>
            Siamo un rifugio dedicato al benessere dei nostri amici felini.
            La vicinanza con il lago ci permette di prenderci cura di loro con tanto amore,
            offrendo cure mediche, attenzione e un ambiente naturale sicuro in attesa di una nuova famiglia.
        </p>

        <p>
            Attraverso il sito puoi conoscere i gatti ospitati nella struttura,
            prenotare una visita per incontrarli e, se lo desideri,
            offrire il tuo tempo come volontario.
        </p>

        <div class="hero-pulsanti">

            <a href="#sezione-gatti" class="btn">
                Conosci i nostri gatti
            </a>

            <a href="turni_volontariato.php" class="btn btn-secondario">
                Diventa volontario
            </a>

        </div>

    </div>

    <!-- Immagine principale del gattile -->
    <div class="hero-immagine">

        <img
            src="assets/img/gattile_lago.png"
            alt="Gatti del Gattile del Lago d'Orta"
        >

    </div>

</section>


<!-- Sezione dedicata agli ultimi due gatti arrivati, come richiesto dalla traccia -->
<section id="nuovi-arrivi">

    <h2>Nuovi Arrivi</h2>

    <p>
        Scopri gli ultimi due gatti arrivati al Gattile del Lago d'Orta.
    </p>

    <!-- Punto di montaggio React per i nuovi arrivi.
         data-num comunica al codice React quanti gatti mostrare. -->
    <div
        id="latest-cats-root"
        data-num="<?php echo $NUM_LATEST_CATS; ?>"
    >
        Caricamento nuovi arrivi...
    </div>

</section>


<!-- Sezione principale delle card gestita dal componente React -->
<section id="sezione-gatti">

    <h2>Tutti i nostri gatti</h2>

    <p>
        Cerca e ordina i gatti disponibili. Se hai effettuato l'accesso,
        puoi selezionare uno o più gatti che desideri conoscere durante una visita.
    </p>

    <!-- Punto di montaggio React per la lista completa dei gatti -->
    <div id="root-gatti">
        Caricamento gatti in corso...
    </div>

</section>


<!-- Form di prenotazione gestito esclusivamente con Vanilla JavaScript -->
<section id="prenota-visita">

    <h2>Prenota una visita conoscitiva</h2>

    <p>
        Seleziona uno o più gatti dalla lista qui sopra e scegli la data e l'ora
        in cui vuoi venirli a conoscere nella struttura.
    </p>

    <!-- Il riepilogo viene aggiornato da prenotazione.js
         quando React comunica la selezione -->
    <div id="riepilogo-gatti">
        Nessun gatto selezionato.
    </div>

    <!-- Contenitore dei messaggi di errore -->
    <div
        id="errore-prenotazione"
        class="errore"
        role="alert"
    ></div>

    <!-- Contenitore dei messaggi di successo -->
    <div
        id="successo-prenotazione"
        class="successo"
        role="status"
    ></div>

    <!-- Il form viene gestito e validato lato client
         esclusivamente dal file prenotazione.js -->
    <form id="prenotazione">

        <label for="data-visita">
            Data e ora della visita:
        </label>

        <input
            type="datetime-local"
            id="data-visita"
            name="data-visita"
            min="<?php echo date('Y-m-d\TH:i'); ?>"
            required
        >

        <button
            type="submit"
            id="btn-prenota-visita"
        >
            Prenota visita
        </button>

    </form>

</section>


<!-- Trasmette dal PHP al JavaScript lo stato di autenticazione dell'utente.
     prenotazione.js utilizza questa variabile per decidere
     se permettere o meno la prenotazione. -->
<script>
    const utenteAutenticato =
        <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>

<!-- Libreria React -->
<script src="https://unpkg.com/react@18/umd/react.development.js"></script>

<!-- ReactDOM permette di montare il componente React nella pagina -->
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>

<!-- Componente React già scritto in JavaScript standard, senza JSX/Babel -->
<script src="components/GattiList.js"></script>

<!-- Vanilla JavaScript che gestisce e valida il form di prenotazione -->
<script src="../scripts/prenotazione.js"></script>

<?php
// Footer comune a tutte le pagine del sito.
// Il footer chiude anche il tag </main>.
include __DIR__ . '/../includes/footer.php';
?>