Benvenuto nella documentazione di **Esse3 API UniCA**.

Tramite queste API potrai in maniera semplice e veloce, accedere alle informazioni relative agli studenti dell'università di Cagliari presenti sulla piattaforma Esse3, e sviluppare le tue applicazioni al meglio, svincolandoti da tutti i dettagli implementativi dell'acquisizione dati. 

## Premessa
**Esse3 API UniCA** vuol'essere un servizio pubblico ed aperto a tutti gli studenti di informatica e non. Il server __non raccoglie in alcun modo__ informazioni personali di alcun tipo come i dati di autenticazione, informazioni relative alla propria carriera universitaria o quant'altro.

Questo set di API è stato sviluppato da [Andrea Corriga]("http://andreacorriga.com", "Andrea Corriga") studente magistrale di Informatica presso l'Università degli studi di Cagliari.

## Guida generale all'utilizzo delle API
Il server, accessibile all'indirizzo: <a href="http://esse3unica.azurewebsites.net/index.php"> http://esse3unica.azurewebsites.net/index.php </a> è impostato per ricevere solo ed esclusivamente chiamate HTTP **POST**. Qualsiasi altra richiesta GET, PUT, DELETE verrà rigettata con il seguente messaggio di errore in formato JSON: 

	
```json
{
    "response": {
        "status": "400",
        "message": "Errore 400, la richiesta risulta essere non valida."
    },
    "data": []
}
```


In modo analogo, tutte le richieste POST restituiranno al mittente un messaggio in formato JSON, variabile a seconda della richiesta effettuata, ma *sempre* con due chiavi principali: *response* e *data*.

```json
{
    "response": {
        "status": "200/400/401/500",
        "message": "_Eventuale messaggio_"
    },
    "data": {
        "eventuali": "dati",
        "richiesti": "tramite",
        "chiamata": "post",
    }
}
```

## Parametri per le chiamate

E' possibile inviare, per ogni chiamata al server, i seguenti parametri: 
 * **request**: (obbligatorio) deve contenere obbligatoriamente uno di questi valori: _"all", "login", utente", "appelliParz", "appelliTot", "libretto", "prenotazioni", "tasse", "imgProfilo"_. Il significato ed il funzionamento di ognuno di questi valori verrà spiegato nelle pagine in dettaglio
 * **user**: (obbligatorio) deve contenere l'username che utilizza l'utente per autenticarsi
 * **password**: (obbligatorio) deve contenere la password che utilizza l'utente per autenticarsi
 * **carriera**: (facoltativo) dev'essere un indice [0 - n]. Il valore delle varie carriere verrà dato in fase di login, ed è valido esclusivamente per gli utenti che posseggono più di una carriera su esse3. In caso di valore mancante e di utente con più di una carrierà, questo valore verrà settato automaticamente a 0.