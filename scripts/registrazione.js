// Attende il caricamento della pagina prima di usare il form.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrazione');
    const errore = document.getElementById('errore_registrazione');

    if (!form) return;

    // Regole richieste per username e password.
    const regexUsername = /^[A-Za-z][A-Za-z0-9_]*$/;

    function passwordValida(p) {
        return /^.{8,16}$/.test(p) &&
               /[A-Z]/.test(p) &&
               /[a-z]/.test(p) &&
               /[0-9]/.test(p) &&
               /[^A-Za-z0-9]/.test(p);
    }

    // addEventListener intercetta l'invio del form per validarlo prima del fetch.
    form.addEventListener('submit', function (evento) {
        evento.preventDefault();
        errore.textContent = '';

        const nome = document.getElementById('nome').value.trim();
        const cognome = document.getElementById('cognome').value.trim();
        const indirizzo = document.getElementById('indirizzo').value.trim();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const conferma = document.getElementById('conferma_password').value;

        if (!nome || !cognome || !indirizzo || !username || !password || !conferma) {
            errore.textContent = 'Tutti i campi sono obbligatori.';
            return;
        }

        if (!regexUsername.test(username)) {
            errore.textContent = 'Lo username deve iniziare con una lettera.';
            return;
        }

        if (!passwordValida(password)) {
            errore.textContent = 'La password deve avere 8-16 caratteri, con maiuscola, minuscola, numero e carattere speciale.';
            return;
        }

        if (password !== conferma) {
            errore.textContent = 'Le password non coincidono.';
            return;
        }

        // Invia i dati validati al backend in formato JSON.
       fetch('api/registrazione_utente.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nome,
                cognome,
                indirizzo,
                username,
                password
            })
        })
        .then(res => res.json().then(dati => ({
            ok: res.ok,
            dati
        })))
        .then(risposta => {
            if (risposta.ok && risposta.dati.successo) {
                window.location.href = 'signin.php';
            } else {
                errore.textContent =
                    risposta.dati.errore || 'Errore nella registrazione.';
            }
        })
        .catch(() => {
            errore.textContent = 'Errore di comunicazione con il server.';
        });
    });
});