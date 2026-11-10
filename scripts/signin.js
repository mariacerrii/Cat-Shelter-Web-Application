// Attende che il contenuto HTML della pagina sia stato caricato prima di cercare il form
// addEventListener --> associa una funzione a un evento senza modificare direttamente l'HTML
document.addEventListener('DOMContentLoaded', function () {

    // Recupera il form di login e i suoi campi tramite i rispettivi id.
    const form = document.getElementById('login');
    const username = document.getElementById('username');
    const password = document.getElementById('password');

    // Se il form non è presente nella pagina, interrompe lo script.
    if (!form) return;

    // addEventListener permette di intercettare l'evento submit del form
    // senza inserire codice JavaScript direttamente nell'HTML.
    form.addEventListener('submit', function (evento) {

        // Rimuove eventuali spazi inseriti prima o dopo lo username.
        const usernameInserito = username.value.trim();
        const passwordInserita = password.value;

        // Controlla che lo username non sia vuoto.
        if (usernameInserito === '') {
            evento.preventDefault();
            alert('Inserisci lo username.');
            username.focus();
            return;
        }

        // Controlla che la password non sia vuota.
        if (passwordInserita === '') {
            evento.preventDefault();
            alert('Inserisci la password.');
            password.focus();
            return;
        }

        // Se i controlli sono superati, il submit prosegue normalmente
        // e i dati vengono inviati a signin.php tramite POST.
    });
});