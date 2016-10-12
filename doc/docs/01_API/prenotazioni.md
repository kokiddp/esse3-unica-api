Questa chiamata serve per ottenere gli esami a cui si è prenotato uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'prenotazioni', 
      'user'      => 'username',
      'password'  => 'password',
      'carriera'  => 0
      );
```

### La risposta
In caso di chiamata andata a buon fine otterremo un _response 200_ ed un campo _data_ così formattato:

```
{
    "response": {
        "status": "200",
        "message": "La richiesta e' stata elaborata correttamente."
    },
    "data": [
        {
            "nomePrenotazione": "Nome della prenotazione",
            "numeroIscrizione": "Numero Iscrizione: 45 su 69",
            "tipoProva": "Tipo Prova: orale",
            "giorno": "gg\/mm\/aaaa",
            "ora": "09:00",
            "edificio": "Edificio",
            "aula": "Aula",
            "riservatoPer": "Matricole pari",
            "docenti": [
                "Nome Cognome",
                "Nome Cognome",
                "Nome Cognome",
            ]
        },
        {},
        {},
    ]
}
```

All'interno di data un avremo un array contenente le informazioni relative agli esami a cui si è prenotato l'utente, con i seguenti indici: 

* **nomePrenotazione** Nome del corso a cui si è prenotato lo studente
* **numeroIscrizione** Numero relativo all'iscrizione. Es: Numero Iscrizione: 45 su 69
* **tipoProva** Indica la tipologia della prova. Es: prova orale
* **giorno** Il giorno in cui si svolge la prova, indicata nel formato gg-mm-aaaa
* **ora** L'ora in cui si svolgerà la prova
* **edificio** L'edificio in cui si svolgerà l'esame
* **aula** L'aula in cui si svolgerà l'esame
* **riservatoPer** Nel caso in cui un esame sia riservato per una determinata categoria di utenti. Es per medicina: Matricole pari
* **docenti** Un array contenente i nominativi dei professori che supervisioneranno la prova