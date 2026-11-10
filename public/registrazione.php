<?php
// Include l'header comune del sito.
// L'header apre anche il tag <main>.
require __DIR__ . '/../includes/header.php';
?>

<section id="sezione-registrazione">

    <h1>Registrati</h1>

    <p>
        Crea un account per prenotare una visita ai nostri gatti
        o partecipare alle attività di volontariato.
    </p>

    <!--
        Il form viene gestito e validato lato client tramite
        Vanilla JavaScript nel file registrazione.js.
    -->
    <form id="registrazione" novalidate>

        <!-- Area utilizzata da JavaScript per mostrare gli errori -->
        <p
            id="errore_registrazione"
            class="errore"
            role="alert"
        ></p>

        <label for="nome">
            Nome
        </label>

        <input
            type="text"
            id="nome"
            name="nome"
            required
        >

        <label for="cognome">
            Cognome
        </label>

        <input
            type="text"
            id="cognome"
            name="cognome"
            required
        >

        <label for="indirizzo">
            Indirizzo
        </label>

        <input
            type="text"
            id="indirizzo"
            name="indirizzo"
            required
        >

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <label for="conferma_password">
            Conferma password
        </label>

        <input
            type="password"
            id="conferma_password"
            name="conferma_password"
            required
        >

        <button
            type="submit"
            id="btn_registrati"
        >
            Registrati
        </button>

    </form>

    <p>
        Hai già un account?
        <a href="signin.php">Accedi</a>.
    </p>

</section>


<!-- Vanilla JavaScript per la gestione e validazione del form -->
<script src="../scripts/registrazione.js"></script>


<?php
// Include il footer comune del sito.
// Il footer chiude anche il tag </main>.
require __DIR__ . '/../includes/footer.php';
?>