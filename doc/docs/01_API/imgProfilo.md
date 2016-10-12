Questa chiamata serve per ottenere l'immagine profilo di uno studente.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio la risposta del server.

```
  postData   = array(
      'request'   => 'imgProfilo', 
      'user'      => 'username',
      'password'  => 'password',
      'carriera'  => 0
      );
```

### La risposta
Otterremo come risposta l'immagine dell'utente, e nessun'altro valore. Qual'ora la chiamata non dovesse andare a buon fine, si riceverà il relativo messaggio di errore.