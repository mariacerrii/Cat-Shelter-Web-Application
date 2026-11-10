// React viene caricato globalmente nella pagina home.php tramite CDN.
// Estrae useEffect e useState dall'oggetto globale React.
const { useEffect, useState } = React;

// Abbreviazione della funzione React.createElement.
// Permette di creare elementi React senza utilizzare JSX.
const e = React.createElement;

// ==================================================
// COMPONENTE REACT GattiList
// ==================================================

const GattiList = ({
    isLogged,
    limit,
    mostraControlli = true
}) => {

    // Stato contenente i gatti ricevuti dall'API.
    const [gatti, setGatti] = useState([]);

    // Stato contenente gli ID dei gatti selezionati.
    const [selectedIds, setSelectedIds] = useState([]);

    // Stato di caricamento dei dati.
    const [caricamento, setCaricamento] = useState(true);

    // Stato per eventuali errori durante il recupero dei dati.
    const [errore, setErrore] = useState(null);

    // Testo inserito dall'utente nel campo di ricerca.
    const [ricerca, setRicerca] = useState('');

    // Criterio utilizzato per ordinare i gatti.
    const [ordinamento, setOrdinamento] =
        useState('data_arrivo');


    // URL dell'API PHP che restituisce i dati dei gatti.
    const API_URL = 'api/api_gatti.php';

    // Classi CSS utilizzate dal componente.
    const containerClass = 'lista-contenitore-gatti';
    const cardClass = 'gatto-card';
    const selectedClass = 'selected';

    // ==================================================
    // IMMAGINI DEI GATTI
    // ==================================================
    // Associa ad alcuni gatti la loro immagine specifica.
    // Se il gatto non è presente nella mappa,
    // viene utilizzata l'immagine ricevuta dall'API,
    // cioè il placeholder.
    function immagineGatto(gatto) {

        const immagini = {
            Fuffi: 'assets/img/Fuffi.png',
            Luna: 'assets/img/Luna.png',
            Oliver: 'assets/img/Oliver.png',
            'Chloè': 'assets/img/Chloe.png'
        };

        return immagini[gatto.nome] || gatto.immagine;
    }


    // ==================================================
    // RECUPERO DATI DALL'API
    // ==================================================
    // useEffect viene eseguito quando il componente
    // viene caricato e ogni volta che cambia "limit".
    useEffect(() => {

        // Se è presente un limite, lo aggiunge alla richiesta.
        const url = limit
            ? `${API_URL}?limit=${limit}`
            : API_URL;

        setCaricamento(true);
        setErrore(null);

        // Recupera i gatti dal backend PHP.
        fetch(url)

            .then(res => {

                if (!res.ok) {
                    throw new Error(
                        'Errore nella risposta del server'
                    );
                }

                return res.json();
            })

            .then(data => {

                // Salva nello stato i gatti ricevuti.
                setGatti(data);

                // Il caricamento è terminato.
                setCaricamento(false);
            })

            .catch(err => {

                setErrore(
                    'Impossibile caricare i gatti. Riprova più tardi.'
                );

                setCaricamento(false);

                console.error(
                    'Errore nel fetch dei gatti:',
                    err
                );
            });

    }, [limit]);


    // ==================================================
    // RICERCA DEI GATTI
    // ==================================================
    const gattiFiltrati = gatti.filter(gatto => {

        const testoRicerca =
            ricerca.trim().toLowerCase();

        // Se il campo è vuoto vengono mostrati tutti i gatti.
        if (testoRicerca === '') {
            return true;
        }

        // Ricerca per nome oppure descrizione.
        return (
            gatto.nome
                .toLowerCase()
                .includes(testoRicerca)
            ||
            gatto.descrizione
                .toLowerCase()
                .includes(testoRicerca)
        );
    });


    // ==================================================
    // ORDINAMENTO DEI GATTI
    // ==================================================
    const gattiOrdinati =
        gattiFiltrati.slice().sort((a, b) => {

            // Età: prima i gatti più giovani.
            if (ordinamento === 'eta') {
                return a.eta - b.eta;
            }

            // Colore del mantello: ordine alfabetico.
            if (ordinamento === 'colore_mantello') {

                return a.colore_mantello.localeCompare(
                    b.colore_mantello
                );
            }

            // Data di arrivo:
            // prima i gatti arrivati più recentemente.
            return (
                new Date(b.data_arrivo)
                -
                new Date(a.data_arrivo)
            );
        });


    // ==================================================
    // SELEZIONE DEI GATTI
    // ==================================================
    const handleSelect = (id) => {
        // Un utente non autenticato può solamente
        // visualizzare i gatti.
        if (!isLogged) {
            return;
        }

        let newSelected;

        // Se il gatto è già selezionato,
        // il suo ID viene rimosso.
        if (selectedIds.includes(id)) {

            newSelected = selectedIds.filter(
                selectedId => selectedId !== id
            );

        } else {

            // Altrimenti viene aggiunto.
            newSelected = [
                ...selectedIds,
                id
            ];
        }

        // Aggiorna lo stato React.
        setSelectedIds(newSelected);


        // ==================================================
        // COMUNICAZIONE REACT -> VANILLA JAVASCRIPT
        // ==================================================
        // Crea un evento DOM personalizzato.
        // Gli ID selezionati vengono inseriti
        // nella proprietà detail.
        const event = new CustomEvent(
            'selectedGattiChanged',
            {
                detail: newSelected
            }
        );

        // L'evento viene inviato sul document.
        // prenotazione.js rimane in ascolto di questo evento.
        document.dispatchEvent(event);
    };


    // ==================================================
    // STATO DI CARICAMENTO
    // ==================================================

    if (caricamento) {

        return e(
            'p',
            {
                className: 'stato-caricamento'
            },
            'Caricamento gatti in corso...'
        );
    }


    // ==================================================
    // STATO DI ERRORE
    // ==================================================
    if (errore) {

        return e(
            'p',
            {
                className: 'errore'
            },
            errore
        );
    }


    // ==================================================
    // CONTROLLI DI RICERCA E ORDINAMENTO
    // ==================================================
    let controlli = null;
    if (mostraControlli) {

        controlli = e(
            'div',
            {
                className: 'controlli-gatti'
            },

            // Label ricerca
            e(
                'label',
                {
                    htmlFor: 'ricerca-gatti'
                },
                'Cerca un gatto:'
            ),

            // Campo ricerca
            e(
                'input',
                {
                    type: 'text',
                    id: 'ricerca-gatti',
                    name: 'ricerca-gatti',
                    placeholder:
                        'Cerca per nome o descrizione...',
                    value: ricerca,

                    onChange: evento =>
                        setRicerca(
                            evento.target.value
                        )
                }
            ),

            // Label ordinamento
            e(
                'label',
                {
                    htmlFor: 'ordinamento-gatti'
                },
                'Ordina i gatti:'
            ),

            // Select ordinamento
            e(
                'select',
                {
                    id: 'ordinamento-gatti',
                    name: 'ordinamento-gatti',
                    value: ordinamento,

                    onChange: evento =>
                        setOrdinamento(
                            evento.target.value
                        )
                },

                e(
                    'option',
                    {
                        value: 'data_arrivo'
                    },
                    'Data di arrivo'
                ),

                e(
                    'option',
                    {
                        value: 'eta'
                    },
                    'Età'
                ),

                e(
                    'option',
                    {
                        value: 'colore_mantello'
                    },
                    'Colore del mantello'
                )
            )
        );
    }


    // ==================================================
    // CREAZIONE DELLE CARD DEI GATTI
    // ==================================================
    const cards = gattiOrdinati.map(gatto => {

        const selezionato =
            selectedIds.includes(gatto.id);

        return e(
            'div',
            {
                key: gatto.id,

                className:
                    cardClass +
                    (selezionato
                        ? ' ' + selectedClass
                        : ''),

                // Se autenticato permette la selezione
                // tramite click.
                onClick: () =>
                    handleSelect(gatto.id),

                style: {
                    cursor:
                        isLogged
                            ? 'pointer'
                            : 'default'
                },

                // Attributi di accessibilità.
                'aria-pressed':
                    isLogged
                        ? selezionato
                        : undefined,

                role:
                    isLogged
                        ? 'button'
                        : undefined,

                tabIndex:
                    isLogged
                        ? 0
                        : undefined,

                // Permette di selezionare una card
                // anche tramite tastiera.
                onKeyDown: evento => {

                    if (
                        isLogged &&
                        (
                            evento.key === 'Enter'
                            ||
                            evento.key === ' '
                        )
                    ) {

                        evento.preventDefault();

                        handleSelect(gatto.id);
                    }
                }
            },

            // Immagine del gatto
            e(
                'img',
                {
                    src: immagineGatto(gatto),
                    alt: `Foto di ${gatto.nome}`
                }
            ),

            // Nome
            e(
                'h3',
                null,
                gatto.nome
            ),

            // Età
            e(
                'p',
                null,
                `Età: ${gatto.eta} mesi`
            ),

            // Colore del mantello
            e(
                'p',
                null,
                `Colore del mantello: ${gatto.colore_mantello}`
            )
        );
    });


    // ==================================================
    // MESSAGGIO NESSUN RISULTATO
    // ==================================================
    const nessunRisultato =
        gattiOrdinati.length === 0
            ? e(
                'p',
                {
                    className: 'nessun-risultato'
                },
                'Nessun gatto trovato con questi criteri.'
            )
            : null;


    // ==================================================
    // OUTPUT DEL COMPONENTE
    // ==================================================
    return e(
        'div',
        {
            className: containerClass
        },

        controlli,

        e(
            'div',
            {
                className: 'griglia-gatti'
            },
            cards
        ),

        nessunRisultato
    );
};


// ==================================================
// MONTAGGIO REACT: NUOVI ARRIVI
// ==================================================
const latestRoot =
    document.getElementById(
        'latest-cats-root'
    );

if (latestRoot) {

    const limit = parseInt(
        latestRoot.dataset.num,
        10
    );

    ReactDOM
        .createRoot(latestRoot)
        .render(

            e(
                GattiList,
                {
                    isLogged: false,
                    limit: limit,
                    mostraControlli: false
                }
            )
        );
}


// ==================================================
// MONTAGGIO REACT: LISTA COMPLETA
// ==================================================
const allCatsRoot =
    document.getElementById(
        'root-gatti'
    );

if (allCatsRoot) {

    ReactDOM
        .createRoot(allCatsRoot)
        .render(

            e(
                GattiList,
                {
                    isLogged:
                        utenteAutenticato
                }
            )
        );
}