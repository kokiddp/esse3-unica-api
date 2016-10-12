Questa chiamata serve per ottenere tutte le informazioni relative ad uno studente, contemporaneamente tramite un unica chiamata **POST**. E' stata inserita questa possibilità per lasciar spazio allo sviluppatore di ottimizzare la propria applicazione (magari al primo avvio) lanciano una sola chiamata REST, senza dover attendere la risposta di 8 chiamate parallele. 

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'all', 
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
    "data": {
        "utente": {},
        "appelliTot": {},
        "appelliParz": {},
        "libretto": {},
        "prenotazioni": {},
        "tasse": {},
    }
}

```
Le informazioni riguardanti i vari oggetti (utente, appelliTot, appelliParz ecc.) verranno spiegate nelle pagine relative all'API relativa.