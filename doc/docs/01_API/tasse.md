Questa chiamata serve per ottenere le informazioni relative alle tasse pagate e da pagare di uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'tasse', 
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
            "idPagamento": "12345",
            "numeroAvvisoPagamento": "12345678912345",
            "anno": "15\/16",
            "descrizione": "Pagamento della rata n° 12345",
            "scadenza": "02\/05\/2016",
            "importo": "\u20ac 40,00",
            "stato": "Da pagare"
        },
        {},
        {},
    ]
}

```

All'interno di data un avremo un array contenente le informazioni delle tasse da pagare, con i seguenti indici: 

* **idPagamento** L'ID univoco del pagamento
* **numeroAvvisoPagamento** ID per il numero del pagamento
* **anno** Anno di riferimento
* **descrizione** Descrizione relativa al pagamento (es: Tassa di iscrizione/immatricolazione)
* **scadenza** Termine ultimo per il pagamento
* **importo** Importo totale del pagamento
* **stato** Lo stato della tassa può essere di tre tipi: "Pagata", "In corso", "Da pagare".

