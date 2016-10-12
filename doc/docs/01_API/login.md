Questa chiamata serve per controllare che i dati d'accesso inseriti dall'utente, passati come paramentro alla chiamata **POST**, siano corretti.

Supponiamo quindi di lanciare una chiamata **POST** con i seguenti parametri e vediamo nel dettaglio le possibili risposte del server.

```
  postData   = array(
      'request'   => 'login', 
      'user'      => 'username',
      'password'  => 'password',
      'carriera'  => null
      );
```

### Login fallito
In caso di autenticazione non andata a buon fine, otterremo un _response 401_ ed un valore di _data_ null.
E' importante notare che questo messaggio verrà mostrato in qualsiasi richiesta, qualora i dati di autenticazione fossero errati.

```json
{
    "response": {
        "status": "401",
        "message": "Errore autenticazione, i dati inseriri risultano non essere corretti."
    },
    "data": []
}
```

### Login effettuato con successo 
In caso di autenticazione corretta e di utente con una sola carriera disponibile otterremo un _response 200_ ed un valode di _data_ null.

```json
{
    "response": {
        "status": "200",
        "message": "La richiesta e' stata elaborata correttamente."
    },
    "data": []
}
```

### Login effettuato con successo con multi carriera
In caso di autenticazione corretta, e di utente con duplice carriera (ma possono essere di più) otterremo un _response 200_ ed un array di _data_.

```json
{
    "response": {
        "status": "200",
        "message": "La richiesta e' stata elaborata correttamente."
    },
    "data": [
        [
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "00\/'00'\/00000"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "TIPOLOGIA CORSO"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "NOME CORSO"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "STATO"
            }
        ],
        [
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "00\/'00'\/00000"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "TIPOLOGIA CORSO"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "NOME CORSO"
            },
            {
                "href": "auth\/studente\/SceltaCarrieraStudente.do;jsessionid=JESSIONID?stu_id=ID_CARRIERA",
                "text": "STATO"
            }
        ],
    ]
}
```