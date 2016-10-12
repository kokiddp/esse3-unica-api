Questa chiamata serve per ottenere le informazioni relative alla carriera universitaria di uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'libretto', 
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
            "nomeCorso": "Nome corso",
            "annoCorso": "1",
            "crediti": "4",
            "stato": "Pianificato",
            "annoFrequenza": "2015-2016",
            "obbligatorio": "Obbligatorio",
            "voto": 30,
            "lode": true,
            "dataConvalida": "gg-mm-aaaa"
        },
        {},
        {},
    ]
}
```

All'interno di data un avremo un array contenente le informazioni relative agli esami sostenuti ed ancora da sostenere, con i seguenti indici: 

* **nomeCorso** Il nome del corso
* **annoCorso** L'anno a cui appartiene il corso
* **crediti** Il peso espresso in crediti 
* **stato** L'esame è stato superato o è ancora da superare? [Pianificato - Frequentato - Superato]
* **annoFrequenza** L'anno in cui è stato frequentato il corso
* **obbligatorio** Indica se l'esame è obbligatorio o è a scelta libera dello studente [Obbligatorio - Esame a scelta]
* **voto** Voto espresso in 30esimi
* **lode** Boleano, se true il voto ha la lode.
* **dataConvalida** Data convalida dell'esame

