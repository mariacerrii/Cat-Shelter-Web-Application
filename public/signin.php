<?php
// Avvia la sessione per poter memorizzare i dati dell'utente autenticato.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connessione al database con utente di sola lettura:
// il login deve solamente cercare e verificare un utente già esistente.
require_once __DIR__ . '/../connessionedb.php';
$conn = connetti_db("lecture", "P@ssw0rd!");

// Variabile che conterrà un eventuale messaggio di errore da mostrare nel form.
$errore_login = '';

// Se esiste il cookie "ultimo_username", lo username viene precompilato.
// La password invece non viene mai salvata né precompilata.
$username_precompilato = isset($_COOKIE['ultimo_username'])
    ? $_COOKIE['ultimo_username']
    : '';

// Il codice di autenticazione viene eseguito solo quando il form viene inviato tramite POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera e pulisce i dati inviati dal form.
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ricordami = isset($_POST['ricordami']);

    // Cerca l'utente tramite username usando un prepared statement.
    $stmt = $conn->prepare(
        'SELECT id, password, is_admin
         FROM utenti
         WHERE username = ?'
    );

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result(
        $id_utente,
        $password_db,
        $is_admin_utente
    );

    // fetch() restituisce true se è stato trovato un utente.
    $utente_trovato = $stmt->fetch();

    $stmt->close();

    // Se l'utente esiste e la password è corretta, crea la sessione di login.
    if (
        $utente_trovato &&
        verifica_password($password, $password_db)
    ) {

        // Genera un nuovo ID di sessione dopo il login
        // per ridurre il rischio di session fixation.
        session_regenerate_id(true);

        // Salva nella sessione i dati necessari durante la navigazione.
        $_SESSION['user_id'] = (int) $id_utente;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = (bool) $is_admin_utente;

        // Se l'utente seleziona "Ricordami",
        // salva solo lo username per 72 ore.
        if ($ricordami) {

            setcookie(
                'ultimo_username',
                $username,
                time() + 259200,
                '/',
                '',
                false,
                true
            );

        } else {

            // Se "Ricordami" non è selezionato,
            // elimina un eventuale cookie precedente.
            setcookie(
                'ultimo_username',
                '',
                time() - 3600,
                '/',
                '',
                false,
                true
            );
        }

        // Dopo il login riuscito reindirizza alla home.
        header('Location: home.php');
        exit;
    }

    // Messaggio volutamente generico:
    // non specifichiamo se sia sbagliato lo username o la password.
    $errore_login = 'Username o password non corretti.';
}

// Header comune del sito.
// L'header apre anche il tag <main>.
require __DIR__ . '/../includes/header.php';
?>


<section id="sezione-login">

    <h1>Accedi</h1>

    <p>
        Accedi al tuo account per prenotare visite
        e partecipare alle attività del gattile.
    </p>

    <!-- Messaggio mostrato solo se il login non va a buon fine -->
    <?php if ($errore_login !== ''): ?>

        <p class="errore" role="alert">
            <?php echo htmlspecialchars($errore_login); ?>
        </p>

    <?php endif; ?>


    <!-- Il form viene validato lato client da signin.js
         e verificato nuovamente lato server in PHP -->
    <form
        method="post"
        id="login"
    >

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            required
            value="<?php echo htmlspecialchars($username_precompilato); ?>"
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

        <label for="ricordami">

            <input
                type="checkbox"
                id="ricordami"
                name="ricordami"
            >

            Ricordami per 72 ore

        </label>

        <button
            type="submit"
            id="btn_login"
        >
            Accedi
        </button>

    </form>

    <p>
        Non hai ancora un account?
        <a href="registrazione.php">Registrati</a>.
    </p>

</section>


<!-- Vanilla JavaScript per la validazione lato client del form di login -->
<script src="../scripts/signin.js"></script>


<?php
// Footer comune del sito.
// Il footer chiude anche il tag </main>.
require __DIR__ . '/../includes/footer.php';
?>