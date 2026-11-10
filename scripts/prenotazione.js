// Aspettiamo il caricamento del DOM prima di cercare gli elementi HTML
// addEventListener --> associa una funzione a un evento senza modificare direttamente l'HTML
document.addEventListener('DOMContentLoaded', function () {

// Salva nella costante form il riferimento al form di prenotazione
  const form = document.getElementById('prenotazione');
// Salva il riferimento al campo datetime-local
  const campoDataVisita = document.getElementById('data-visita');
// Salva il riferimento all'elemento HTML in cui verrà mostrato il riepilogo dei gatti selezionati.
  const riepilogoGatti = document.getElementById('riepilogo-gatti');
// Salva il riferimento all'elemento HTML utilizzato per visualizzare eventuali messaggi di errore.
  const messaggioErrore = document.getElementById('errore-prenotazione');
// Salva il riferimento all'elemento HTML utilizzato per visualizzare il messaggio di avvenuta prenotazione.
  const messaggioSuccesso = document.getElementById('successo-prenotazione');

  // Array che contiene gli ID dei gatti selezionati nel componente React
  let gattiSelezionati = [];
  // Se il form non esiste interrompiamo lo script per evitare errori
  if (!form) return;
  // Funzioni di supporto per mostrare i messaggi senza duplicare codice
  function mostraErrore(testo) {
    if (messaggioErrore) messaggioErrore.textContent = testo;
    if (messaggioSuccesso) messaggioSuccesso.textContent = '';
  }
  function mostraSuccesso(testo) {
    if (messaggioSuccesso) messaggioSuccesso.textContent = testo;
    if (messaggioErrore) messaggioErrore.textContent = '';
  }

  // Aggiorna il riepilogo del numero di gatti selezionati
  function aggiornaRiepilogo() {
    if (!riepilogoGatti) return;
    if (gattiSelezionati.length === 0) {
      riepilogoGatti.textContent = 'Nessun gatto selezionato.';
    } else if (gattiSelezionati.length === 1) {
      riepilogoGatti.textContent = 'Hai selezionato 1 gatto per la visita.';
    } else {
      riepilogoGatti.textContent =
        'Hai selezionato ' + gattiSelezionati.length + ' gatti per la visita.';
    }
  }

  // Riceve da React gli ID dei gatti selezionati tramite CustomEvent
  // addEventListener --> rimane in ascolto dell'evento "selectedGattiChanged"
  // generato da GattiList.jsx ogni volta che cambia la selezione
  document.addEventListener('selectedGattiChanged', function (evento) {
    // detail --> proprietà del CustomEvent utilizzata per trasportare i dati
    gattiSelezionati = evento.detail;
    aggiornaRiepilogo();
  });

  // Avviso immediato per chi non è autenticato
  if (typeof utenteAutenticato !== 'undefined' && !utenteAutenticato) {
    mostraErrore(
      'Attenzione! Per prenotare una visita devi prima effettuare il login o registrarti!!'
    );
  }

  // Gestisce il form esclusivamente con Vanilla JavaScript.
  // L'evento submit viene generato quando l'utente prova a inviare il form.
  form.addEventListener('submit', function (evento) {

    // Blocca l'invio tradizionale del form e il ricaricamento della pagina.
    evento.preventDefault();

    if (messaggioErrore) messaggioErrore.textContent = '';
    if (messaggioSuccesso) messaggioSuccesso.textContent = '';

    // VALIDAZIONE LATO CLIENT

    // Controlla che l'utente sia autenticato.
    if (typeof utenteAutenticato === 'undefined' || !utenteAutenticato) {
      mostraErrore(
        'Attenzione! Per prenotare una visita devi prima effettuare il login o registrarti!!'
      );
      return;
    }

    // Deve essere selezionato ALMENO un gatto
    if (gattiSelezionati.length === 0) {
      mostraErrore(
        'Necessario selezionare almeno un gatto prima di prenotare la visita.'
      );
      return;
    }

    // Deve essere stata scelta una data e un'ora
    if (!campoDataVisita || campoDataVisita.value === '') {
      mostraErrore(
        'Seleziona una data e un orario per la visita'
      );
      return;
    }

    // Trasforma la data inserita in un oggetto Date per poterla confrontare
    const dataScelta = new Date(campoDataVisita.value);

    // Non permette di prenotare una visita nel passato
    if (dataScelta <= new Date()) {
      mostraErrore(
        'La data della visita deve essere successiva al momento attuale.'
      );
      return;
    }

    // datetime-local restituisce ad esempio "2026-08-20T15:30".
    // Lo convertiamo in "2026-08-20 15:30:00", formato compatibile con MySQL DATETIME.
    const dataOraSql =
      campoDataVisita.value.replace('T', ' ') + ':00';
    // Oggetto contenente i dati che verranno inviati al backend PHP.
    const datiPrenotazione = {
      data_ora: dataOraSql,
      gatti: gattiSelezionati
    };

    // Invia la prenotazione al backend PHP tramite POST in formato JSON
    fetch('api/prenotazione.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(datiPrenotazione)
    })

      // Converte la risposta JSON del server in un oggetto JavaScript
      .then(function (res) {
        return res.json().then(function (dati) {
          return {
            ok: res.ok,
            dati: dati
          };
        });
      })

      // Analizza la risposta ricevuta dal backend
      .then(function (risposta) {
        if (risposta.ok && risposta.dati.successo) {
          mostraSuccesso(
            risposta.dati.messaggio ||
            'Complimenti! Prenotazione della visita effettuata correttamente!'
          );

          // Dopo il successo pulisce il form e la selezione locale.
          form.reset();
          gattiSelezionati = [];
          aggiornaRiepilogo();

        } else {
          mostraErrore(
            risposta.dati.errore ||
            'Errore durante la prenotazione della visita.'
          );
        }
      })

      // Gestisce eventuali errori di comunicazione con il server.
      .catch(function (errore) {
        mostraErrore(
          'Errore di comunicazione con il server. Riprovare più tardi.'
        );

        // Il dettaglio tecnico viene mantenuto nella console per il debug.
        console.error('Errore nella prenotazione:', errore);
      });
  });
});