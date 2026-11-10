// Attende che la pagina sia completamente caricata.
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('volontariato');
    const messaggio = document.getElementById('messaggio_volontariato');
    const checkboxes = document.querySelectorAll('input[name="fasce[]"]');

    if (!form) return;

    // Mostra i messaggi nella pagina.
    function mostraMessaggio(testo, tipo) {
        messaggio.textContent = testo;
        messaggio.className = tipo;
    }

    // Converte il valore della checkbox nello stesso formato
    // restituito da conteggio_turni.php.
    function normalizzaFascia(valore) {
        return valore
            .replace(/\s*[–—-]\s*/g, ' - ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // Controlla quanti volontari sono già presenti
    // in ciascuna fascia oraria.
    function aggiornaDisponibilita() {

        fetch('api/conteggio_turni.php', {
            method: 'GET',
            cache: 'no-store'
        })
        .then(function (response) {

            if (!response.ok) {
                throw new Error('Errore HTTP: ' + response.status);
            }

            return response.json();
        })
        .then(function (dati) {

            if (!dati.successo) {
                mostraMessaggio(
                    dati.errore || 'Errore nel recupero dei turni.',
                    'errore'
                );
                return;
            }

            checkboxes.forEach(function (checkbox) {

                const fascia = normalizzaFascia(checkbox.value);
                const numeroVolontari = Number(dati.turni[fascia] || 0);

                console.log(
                    'Fascia:',
                    fascia,
                    'Volontari:',
                    numeroVolontari
                );

                // Se ci sono già due volontari,
                // la fascia non può essere selezionata.
                if (numeroVolontari >= 2) {
                    checkbox.checked = false;
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        })
        .catch(function (errore) {

            console.error('Errore disponibilità:', errore);

            mostraMessaggio(
                'Errore di comunicazione con il server.',
                'errore'
            );
        });
    }

    // Avvisa l'utente se non ha effettuato il login.
    if (
        typeof utenteAutenticato !== 'undefined' &&
        !utenteAutenticato
    ) {
        mostraMessaggio(
            'Effettua il login o la registrazione per prenotare un turno.',
            'avviso'
        );
    }

    // Controlla le disponibilità appena viene caricata la pagina.
    aggiornaDisponibilita();

    // Gestisce la prenotazione.
    form.addEventListener('submit', function (evento) {

        evento.preventDefault();

        // Controllo autenticazione.
        if (
            typeof utenteAutenticato === 'undefined' ||
            !utenteAutenticato
        ) {
            mostraMessaggio(
                'Devi effettuare il login prima di prenotare un turno.',
                'avviso'
            );
            return;
        }

        // Recupera le fasce selezionate.
        const fasceSelezionate = Array.from(checkboxes)
            .filter(function (checkbox) {
                return checkbox.checked && !checkbox.disabled;
            })
            .map(function (checkbox) {
                return normalizzaFascia(checkbox.value);
            });

        // Deve essere selezionata almeno una fascia.
        if (fasceSelezionate.length === 0) {
            mostraMessaggio(
                'Seleziona almeno una fascia oraria disponibile.',
                'errore'
            );
            return;
        }

        // Invia la prenotazione al server.
        fetch('api/prenota_turno.php', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                fasce: fasceSelezionate
            })
        })

        .then(function (response) {

            return response.json().then(function (dati) {

                return {
                    ok: response.ok,
                    dati: dati
                };

            });
        })

        .then(function (risposta) {

            // Prenotazione riuscita.
            if (
                risposta.ok &&
                risposta.dati.successo
            ) {

                mostraMessaggio(
                    risposta.dati.messaggio ||
                    'Turno prenotato correttamente!',
                    'successo'
                );

                // Deseleziona le checkbox.
                form.reset();

                // Ricontrolla immediatamente le disponibilità.
                aggiornaDisponibilita();

            } else {

                // Errore restituito dal PHP.
                mostraMessaggio(
                    risposta.dati.errore ||
                    'Errore durante la prenotazione del turno.',
                    'errore'
                );

                // Aggiorna comunque le disponibilità.
                aggiornaDisponibilita();
            }
        })

        .catch(function (errore) {

            console.error('Errore prenotazione:', errore);

            mostraMessaggio(
                'Errore di comunicazione con il server.',
                'errore'
            );
        });
    });

});