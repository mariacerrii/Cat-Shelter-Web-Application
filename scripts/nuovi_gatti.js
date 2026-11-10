// Attende il caricamento della pagina prima di utilizzare il form.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('new_gatto');
    const errore = document.getElementById('errore_gatto');
    const successo = document.getElementById('successo_gatto');

    if (!form) return;

    // addEventListener intercetta il submit per controllare i dati prima dell'invio.
    form.addEventListener('submit', function (evento) {
        evento.preventDefault();

        errore.textContent = '';
        successo.textContent = '';

        // Raccoglie i dati inseriti nel form.
        const gatto = {
            nome: document.getElementById('nome_gatto').value.trim(),
            descrizione: document.getElementById('descrizione_gatto').value.trim(),
            peso: parseFloat(document.getElementById('peso_gatto').value),
            colore_mantello: document.getElementById('colore_mantello_gatto').value.trim(),
            lunghezza_pelo: document.getElementById('lunghezza_pelo_gatto').value,
            razza: document.getElementById('razza_gatto').value.trim(),
            colore_occhi: document.getElementById('colore_occhi_gatto').value.trim(),
            eta: parseInt(document.getElementById('eta_gatto').value, 10),
            sesso: document.getElementById('sesso_gatto').value,
            data_arrivo: document.getElementById('data_arrivo_gatto').value
        };

        // Controlla che tutti i campi obbligatori siano compilati.
        if (!gatto.nome || !gatto.descrizione || !gatto.colore_mantello ||
            !gatto.lunghezza_pelo || !gatto.razza || !gatto.colore_occhi ||
            !gatto.sesso || !gatto.data_arrivo) {

            errore.textContent = 'Compila tutti i campi obbligatori.';
            return;
        }

        // Peso e età devono avere valori validi.
        if (isNaN(gatto.peso) || gatto.peso <= 0) {
            errore.textContent = 'Il peso deve essere maggiore di zero.';
            return;
        }

        if (isNaN(gatto.eta) || gatto.eta < 0) {
            errore.textContent = "L'età non può essere negativa.";
            return;
        }

        // Un gatto non può avere una data di arrivo futura.
        if (new Date(gatto.data_arrivo + 'T00:00:00') > new Date()) {
            errore.textContent = 'La data di arrivo non può essere nel futuro.';
            return;
        }

        // Invia i dati al backend PHP in formato JSON.
        fetch('api/inserisci_gatto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(gatto)
        })
        .then(res => res.json().then(dati => ({
            ok: res.ok,
            dati: dati
        })))
        .then(risposta => {
            if (risposta.ok && risposta.dati.successo) {
                successo.textContent = 'Gatto inserito correttamente!';
                form.reset();
            } else {
                errore.textContent =
                    risposta.dati.errore || "Errore nell'inserimento del gatto.";
            }
        })
        .catch(() => {
            errore.textContent = 'Errore di comunicazione con il server.';
        });
    });
});