<?php
// Avvia la sessione se non è già attiva.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// La pagina è accessibile solo agli amministratori.
// Durante il login il ruolo admin viene salvato nella variabile di sessione is_admin.
if (empty($_SESSION['is_admin'])) {
    header('Location: home.php');
    exit;
}

// Include l'header comune del sito.
// L'header apre anche il tag <main>.
require __DIR__ . '/../includes/header.php';
?>


<section id="sezione-nuovo-gatto">

    <h1>Inserisci un nuovo gatto</h1>

    <p>
        Compila tutti i campi per aggiungere un nuovo gatto
        al database del gattile.
    </p>

    <!-- Messaggi aggiornati dal JavaScript -->
    <p id="errore_gatto" class="errore" role="alert"></p>
    <p id="successo_gatto" class="successo" role="status"></p>

    <!--
        Il form viene gestito e validato lato client
        esclusivamente tramite Vanilla JavaScript.
    -->
    <form id="new_gatto" novalidate>

        <label for="nome_gatto">Nome *</label>
        <input
            type="text"
            id="nome_gatto"
            name="nome"
            required
        >

        <label for="descrizione_gatto">Descrizione *</label>
        <textarea
            id="descrizione_gatto"
            name="descrizione"
            required
        ></textarea>

        <label for="peso_gatto">Peso (kg) *</label>
        <input
            type="number"
            id="peso_gatto"
            name="peso"
            step="0.01"
            min="0.01"
            required
        >

        <label for="colore_mantello_gatto">
            Colore del mantello *
        </label>
        <input
            type="text"
            id="colore_mantello_gatto"
            name="colore_mantello"
            required
        >

        <label for="lunghezza_pelo_gatto">
            Lunghezza del pelo *
        </label>
        <select
            id="lunghezza_pelo_gatto"
            name="lunghezza_pelo"
            required
        >
            <option value="">-- Seleziona --</option>
            <option value="Corto">Corto</option>
            <option value="Medio">Medio</option>
            <option value="Lungo">Lungo</option>
        </select>

        <label for="razza_gatto">Razza *</label>
        <input
            type="text"
            id="razza_gatto"
            name="razza"
            required
        >

        <label for="colore_occhi_gatto">
            Colore degli occhi *
        </label>
        <input
            type="text"
            id="colore_occhi_gatto"
            name="colore_occhi"
            required
        >

        <label for="eta_gatto">Età (mesi) *</label>
        <input
            type="number"
            id="eta_gatto"
            name="eta"
            min="0"
            required
        >

        <label for="sesso_gatto">Sesso *</label>
        <select
            id="sesso_gatto"
            name="sesso"
            required
        >
            <option value="">-- Seleziona --</option>
            <option value="M">Maschio</option>
            <option value="F">Femmina</option>
        </select>

        <label for="data_arrivo_gatto">
            Data di arrivo *
        </label>
        <input
            type="date"
            id="data_arrivo_gatto"
            name="data_arrivo"
            required
        >

        <button
            type="submit"
            id="btn_inserisci_gatto"
        >
            Salva gatto
        </button>

    </form>

</section>


<!-- Vanilla JavaScript per gestione e validazione del form -->
<script src="../scripts/nuovi_gatti.js"></script>


<?php
// Include il footer comune.
// Il footer chiude anche il tag </main>.
require __DIR__ . '/../includes/footer.php';
?>