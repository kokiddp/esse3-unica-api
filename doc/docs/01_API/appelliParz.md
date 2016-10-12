Questa chiamata serve per ottenere le informazioni relative agli appelli parziali disponibili per uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'appelliParz', 
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
            "prenotabile": true,
            "nomeCorso": "Nome Corso",
            "dataAppello": "20\/07\/2016",
            "dataIscrizione": "05\/07\/201618\/07\/2016",
            "descrizione": "Descrizione del corso",
            "presidente": "Nome Presidente",
            "cfu": "4"
        },
        {},
        {},
    ]
}
```

All'interno di data un avremo un array contenente le informazioni relative agli appelli parziali disponibili, con i seguenti indici: 

* **prenotabile** E' già possibile prenotarsi a questo appello? [true - false]
* **nomeCorso** Nome del corso 
* **dataAppello** Data in cui si svolgerà l'esame
* **dataIscrizione** Data di apertura e chiusura iscrizione all'esame
* **descrizione** Descrizione dell'esame
* **presidente** Presidente d'esame
* **cfu** Peso dell'esame espresso in crediti