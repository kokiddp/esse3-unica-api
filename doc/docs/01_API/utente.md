Questa chiamata serve per ottenere le informazioni anagrafiche di base di uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'utente', 
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
        "nome": "Nome utente",
        "cognome": "Cognome utente",
        "matricolaL": "00\/00\/00000",
        "matricolaS": "00000",
        "email": "email@example.com",
        "emailAteneo": "email@studenti.unica.it",
        "telefono": "+39 343333333\u00a0",
        "statoCarriera": "attiva"
    }
}
```

All'interno di data un avremo un array contenente le informazioni anagrafiche dell'utente, con i seguenti indici: 

* **nome** Il nome dello studente
* **cognome** Il cognome dello studente
* **matricolaL** La matricola dello studente nel formato completo xx/xx/xxxx
* **matricolaS** La matricola dello studente nel formato ridotto xxxx
* **email** L'email usata in fase di registrazione nella piattaforma
* **emailAteneo** L'email fornita dall'ateneo @studenti.unica.it
* **telefono** Il numero di telefono dello studente
* **statoCarriera** L'utente potrà avere la carriera "attiva" oppure "registrata" quando è ancora in fase di perfezionamento da parte della segreteria

