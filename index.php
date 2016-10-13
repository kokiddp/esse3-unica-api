<?php

/**
 *
 * PHP version 5
 *
 *
 * @author     Andrea Corriga <me@andreacorriga.com>
 * @copyright  2016 Andrea Corriga
 * @license    
 * @version    1.0
 * @link       http://andreacorriga.com
 *
 *
 */

ini_set('display_errors',1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
//error_reporting(E_ALL);
//ini_set('display_errors', 0);
//error_reporting(0);


 //Includo la libreria
include('lib/ParseEsse3.php');

/**
 * Le richieste attualmente accettate sono le seguenti
 * all 				=> scarica tutte le informazioni dell'utente
 * login 			=> testa i dati inviati per il login. Restituisce le carriere disponibili in caso di multicarriera
 * utente 			=> restituisce le informazioni di base dell'utente
 * appelliTot 		=> restituisce tutti gli appelli totali disponibili
 * appelliParz 		=> restituisce tutti gli appelli parziali disponibili
 * libretto 		=> restituisce tutti i corsi presenti nella pagina carriera
 * prenotazioni 	=> restituisce tutti gli esami a cui ci si è prenotati
 * tasse 			=> restituisce tutte le tasse pagate e da apgare
 * imgProfilo 		=> stampa l'immagine profilo dell'utente
 */	
$acceptedReq	= array("all", "login", "utente", "appelliTot", "appelliParz", "libretto", "prenotazioni", "tasse", "imgProfilo");


// Il server accetterà solamente richieste POST, per una maggiore sicurezza dei dati di autenticazione dell'utente
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$request 	= isset($_POST['request']) ? $_POST['request'] : null; 		// La tipoologia di dato richiesto
	$user		= isset($_POST['user']) ? $_POST['user'] : null; 			// l'username per il login dell'utente
	$password	= isset($_POST['password']) ? $_POST['password'] : null;	// Password di autenticazione
	$carriera 	= isset($_POST['carriera']) ? $_POST['carriera'] : 0;		// Indice della carriera scelta, se non viene passato alcun parametro uso la prima carriera

	if( in_array( $request , $acceptedReq ) ){ //Controllo che la richiesta sia valida

		$esse3 = new ParseEsse3($user, $password); // Creo l'oggetto con i valori passati come parametro
		$login = $esse3->parseLogin();

		if(Message::getStatus($login) == 200) {	
			
			if($carriera == null)
				$carriera = 0;

			if($esse3->getHasCarriere())
				$esse3->setCarrieraScelta($carriera); // Setto la carriera nel caso in cui l'utente abbia più carriere disponibili	
		
			switch($request){
				// In questo caso scarico tutte le informazioni dell'utente e le inserisco in un unico array
				case "all": 
					$appelliTot			= $esse3->parseAppelli("t");
					$appelliParz		= $esse3->parseAppelli("p");
					$librettoArr 		= $esse3->parseLibretto();
					$prenotazioniArr 	= $esse3->parsePrenotazioni();
					$tasseArr 			= $esse3->parseTasse();
					$utente 			= $esse3->parseUtente();

					$appelliT 		= array();
					$appelliP 		= array();
					$libretto 		= array();
					$prenotazioni 	= array();
					$tasse 			= array();

					if(is_array($appelliTot)){
						foreach($appelliTot as $a)
							$appelliT[] = $a->getAppello();
					}

					if(is_array($appelliParz)){
						foreach($appelliParz as $a)
							$appelliP[] = $a->getAppello();
					}

					if(is_array($librettoArr)){
						foreach($librettoArr as $l)
							$libretto[] = $l->getLibretto();
					}

					if(is_array($prenotazioniArr)){
						foreach($prenotazioniArr as $p)
							$prenotazioni[] = $p->getPrenotazione();
					}

					if(is_array($tasseArr)){
						foreach($tasseArr as $t)
							$tasse[] = $t->getTassa();
					}

					$allValue = array(
										"utente" 		=> $utente,
										"appelliTot"	=> $appelliT,
										"appelliParz"	=> $appelliP,
										"libretto"		=> $libretto,
										"prenotazioni"	=> $prenotazioni,
										"tasse"			=> $tasse
									);
					Message::handleMessage(Message::get_200(), $allValue);
				break;

				case "login":
					$carriere = array();
					
					if($esse3->getHasCarriere())
						$carriere = $esse3->getCarriere();

					Message::handleMessage(Message::get_200(), $carriere);
				break;
				// Restituisco le informazioni di base dell'utente
				case "utente": 
					$utente = $esse3->parseUtente();
					if($utente)
						Message::handleMessage(Message::get_200(), $utente);
					else
						Message::handleMessage(Message::get_500());
				break;

				// Restituisco gli appelli totali
				case "appelliTot": 
					$appelliTot	= $esse3->parseAppelli("t");
					$appelli = array();

					if(is_array($appelliTot)){
						foreach($appelliTot as $a)
							$appelli[] = $a->getAppello();

						Message::handleMessage(Message::get_200(), $appelli);
					}
					else
						Message::handleMessage(Message::get_500());
				break;

				// Restituisco gli appelli parziali
				case "appelliParz": 
					$appelliParz	= $esse3->parseAppelli("p");					
					$appelli 		= array();

					if(is_array($appelliParz)){
						foreach($appelliParz as $a)
							$appelli[] = $a->getAppello();

						Message::handleMessage(Message::get_200(), $appelli);
					}
					else
						Message::handleMessage(Message::get_500());

				break;

				// Restituisco le informazioni della carriera
				case "libretto": 
					$librettoArr = $esse3->parseLibretto();
					$libretto = array();

					if(is_array($librettoArr)){
						foreach($librettoArr as $l)
							$libretto[] = $l->getLibretto();

						Message::handleMessage(Message::get_200(), $libretto);
					}
					else
						Message::handleMessage(Message::get_500());
				break;

				// Restituisco le informazioni degli appelli a cui ci si è prenotati
				case "prenotazioni": 
					$prenotazioniArr = $esse3->parsePrenotazioni();
					$prenotazioni = array();

					if(is_array($prenotazioniArr)){
						foreach($prenotazioniArr as $p)
							$prenotazioni[] = $p->getPrenotazione();

						Message::handleMessage(Message::get_200(), $prenotazioni);
					}
					else
						Message::handleMessage(Message::get_500());
				break;

				// Restituisco le informazioni relative alle tasse pagate e da pagare
				case "tasse": 
					$tasseArr	= $esse3->parseTasse();
					$tasse = array();

					if(is_array($tasseArr)){
						foreach($tasseArr as $t)
							$tasse[] = $t->getTassa();

						Message::handleMessage(Message::get_200(), $tasse);
					}
					else
						Message::handleMessage(Message::get_500());
				break;	

				// Stampo l'immagine del profilo
				case "imgProfilo":
					$esse3->parseImmagineProfilo(); // Immagine profilo
					header('Content-Type: image/png');
					echo $esse3->getImmagineProfilo()[0];
					//echo 'data:image/png;base64,' . base64_encode($esse3->getImmagineProfilo()[0]);
					//echo base64_encode($esse3->getImmagineProfilo()[0]);
				break;
			}
		}
		else
			Message::handleMessage($login); // Se il login non va a buon fine, stampo l'errore
	}
	else
		Message::handleMessage(Message::get_400()); // Se viene effettuata una richiesta POST, ma con una richiesta errata lancio un errore
}
else 
	Message::handleMessage(Message::get_400()); // Se non ricevo chiamate POST, lancio un'errore 400

// Google Analytics, non incluso nel repository
//include_once("analytics/analyticstracking.php");