<?php

/**
 *
 * PHP version 5
 *
 *
 * @author Andrea Corriga <me@andreacorriga.com>
 * @copyright 2018 Andrea Corriga
 * @license	
 * @version	1.1
 * @link http://andreacorriga.com
 *
 *
 */

include('Esse3.php');
include('Message.php');

include('model/Utente.php');
include('model/Appello.php');
include('model/Libretto.php');
include('model/Tassa.php');
include('model/Prenotazione.php');

class ParseEsse3 extends Esse3 {


	public function __construct($username, $password) {
			parent::__construct($username, $password); // uso il costruttore della superclasse
	}

	/**
	 * @return Array Restituisce in array le varie carriere disponibili per l'utente
	 */
	public function parseLogin(){
		$response =  $this->getLogin();

		if($response[1] == 200){ // Il login va a buon fine
			$doc = new DOMDocument();
			$doc->loadHTML($response[0]);


			$list = $doc->getElementsByTagName("title");
			if ($list->length > 0) {
				$title = $list->item(0)->textContent;
			}

			
			if($title == 'Home Studente, Università di UNICA'){
				$this->setHasCarriere(false);
			}

			if($title == 'Scegli carriera, Università di UNICA'){ //Scelgo la carriera

				$xpath = new DOMXpath($doc);

				$table = $doc->getElementById("gu_table_sceltacarriera"); //$xpath->query('//table[@class="detail_table"]');
				
				$rows = $table->getElementsByTagName("tr");

				$counter = 0; 
				foreach($rows as $row) {

					if($counter > 0){
						$line = $row->getElementsByTagName("td");

						$arr = $row->getElementsByTagName("a");
						
						foreach($arr as $item) {
							$href =  $item->getAttribute("href");
							$text = $line[1]->textContent . ' - ' . $line[2]->textContent;
							$links[] = array( 'href' => $href, 'text' => $text );
						}
					}
					$counter++;
				}

				$this->setHasCarriere(true);
				$this->setCarriere( $links, 2);
				
			}
			
			return Message::get_200();
		} // endif status == 200

		if($response[1] == 401)
			return Message::get_401();
	}
	
	/**
	 * @return Object Utente. Restituisce l'oggetto completo dell'utente
	 * 			parsando l'html della pagina Home.do.
	*/
	public function parseUtente()
	{
		$response = $this->getInfoUtente(); // Ottengo la pagina Html completa della home Esse3
		
		$doc = new DOMDocument();
		$doc->loadHTML($response[0]);
		$dom->preserveWhiteSpace = false;
		$values = array();

		// Controllo il titolo della pagina. La carriera potrebbe essere ancora in fase di perfezionamento
		$list = $doc->getElementsByTagName("title");
		if ($list->length > 0) {
			$title = $list->item(0)->textContent;
		}

		if($title == 'Home Utente Registrato, Università di UNICA'){
			$datiPersonaliHtml 	= $doc->getElementById("gu-homepageRegistrato-cp1Child");
			$ddHtml 			= $datiPersonaliHtml->getElementsByTagName("dd");
			$matricola = $doc->getElementById("header");

			$datiPersonali = array();
			// $ddHtml contiene un sacco di informazioni superflue, utilizzando il metodo getArrayForParseUtente
			// faccio un po di pulizia
			foreach ($ddHtml as $node) {
				$datiPersonali[] = $this->getArrayForParseUtente($node);
			}
			// Credo un oggetto utente, uso i setter per impostare i dati e restituisco l'oggetto
			$utente = new Utente();
			$utente->setNome($datiPersonali[1]['description'][0]['#text']);
			$utente->setCognome($datiPersonali[1]['description'][0]['#text']);
			$utente->setEmail($datiPersonali[4]['description'][0]['#text']);
			$utente->setTelefono($datiPersonali[5]['description'][0]['#text']);
			$utente->setMatricola($matricola->textContent);
			$utente->setStatoCarriera("registrata");
			return $utente->getUtente();
		}

		// Provo a leggere l'html leggendo i vari id e tag della pagina. Alcune volte la lettura fallisce
		// per questo utilizzo un try catch
		if($title == 'Home Studente, Università di UNICA'){ 
			$datiPersonaliHtml = $doc->getElementById("gu-hpstu-boxDatiPersonali");
			
			if(empty($datiPersonaliHtml)){ // Se è vuoto c'è un problema, restituisco null per sicurezza
				$utente = new Utente();
				return $utente->getUtente(); // Sarà vuoto
			}
			
			$ddHtml = $datiPersonaliHtml->getElementsByTagName("dd");
			$matricola = $doc->getElementById("gu-header"); // get matricola
			//print_r($ddHtml[1]);

			// Credo un oggetto utente, uso i setter per impostare i dati e restituisco l'oggetto
			$utente = new Utente();
			$utente->setNome($ddHtml[1]->textContent);
			$utente->setCognome($ddHtml[1]->textContent);
			$utente->setEmail(explode(" ", $ddHtml[4]->textContent)[0]); // use explode because of space and "modifica" link in the page
			$utente->setEmailAteneo($ddHtml[5]->textContent);
			$utente->setTelefono($ddHtml[6]->textContent);		
			$utente->setMatricola($matricola->textContent);
			$utente->setStatoCarriera("attiva");

			// Ottengo media ponderata e aritmetica
			$riepilogoEsami = $doc->getElementById("gu-boxRiepilogoEsami");
			// Non ci sono errori quindi prendo gli elementi
			if(!empty($riepilogoEsami)){
				$ddHtml = $riepilogoEsami->getElementsByTagName("dd");
				$utente->setEsamiRegistrati($ddHtml[0]->textContent);
				$utente->setMediaAritmetica($ddHtml[1]->textContent);
				$utente->setMediaPonderata($ddHtml[2]->textContent);
			}

			$libretto = $this->parseLibretto();
			
			if(empty($libretto) == false)
				$utente->setCfu($libretto);

			return $utente->getUtente();
		}
	}


	/**
	 * @return array of Libretto
	 * Questa funzione legge tutta la tabella contenente i voti della propria carriera (libretto)
	 * Legge tutte le colonne td, se la colonna esiste ed esiste il suo contenuto
	 * salva il valore nell'array, altrimenti controlla che non sia presente un immagine.
	 * Se il corso risulta essere obbligatorio, l'array conterrà 8 elementi, altrimenti 7.
	 *  
	*/
	public function parseLibretto()
	{
		$response = $this->getLibretto(); // Ottengo la pagina Html completa della carriera Esse3
		
		$doc = new DOMDocument();
		$doc->validateOnParse = true;
		$doc->loadHTML($response[0]);

		$xpath = new DOMXPath($doc);
		$table = $xpath->query("//*[@class='detail_table']")->item(0);

		// Se non esiste la tabella, perché magari non è ancora stato inserito un esame, restituisco null
		if(empty($table))
			return null;
		elseif($table->textContent == "Non è stata ancora sostenuta alcuna attività didattica.")
			return null;

		$rows 		= $table->getElementsByTagName("tr");
		//$libretto 	= null;
		$esami 		= null;

		foreach ($rows as $key => $row) {
			$cells = $row->getElementsByTagName('td');
			
			if($cells->length == 13){
				foreach ($cells as $col => $cell) {
					//echo '<pre>'; print_r($cell);
			  		if ($cell->nodeName == 'td') {

			  			if($cell->textContent != null)
							$esami[$key][] = $cell->textContent;

						foreach($cell->getElementsByTagName("img") as $img)
							$esami[$key][] =  $img->getAttribute("src");
						
				 	} 
				} // end of foreach($cells as $cell )
			} // $cells->lenght == 13

			if($cells->length == 12){
				foreach ($cells as $col => $cell) {
					//echo '<pre>'; print_r($cell);
			  		if ($cell->nodeName == 'td') {

			  			// Col != 0 perché negli esami di ingegneria ci sono i corsi integrati e la prima colonna viene ereditata dalla prima riga inerente a quel blocco di esami
			  			if($cell->textContent != null && $col != 0)
							$esami[$key][] = $cell->textContent;

						foreach($cell->getElementsByTagName("img") as $img)
							$esami[$key][] =  $img->getAttribute("src") == "images/figlio_raggr.gif" ? $esami[$key-1][0] : $img->getAttribute("src"); //echo $img->getAttribute("src") .'<br>'; 

						foreach($cell->getElementsByTagName("a") as $img)
							$esami[$key][] =  $img->textContent; //echo $img->textContent.'<br>';
						
				 	} 
				} // end of foreach($cells as $cell )
			} // $cells->lenght == 13

			$libretto[$key] = new Libretto($esami[$key]);
		} // end of foreach ($rows as $key => $row)

		// elimino il primo elemento, poichè risulta essere vuoto
		unset($libretto[0]);
		return $libretto; 
	}

	/**
	 * @return array of Tasse
	 * Questa funzione legge tutta la tabella contenente i voti della propria carriera (libretto)
	 * Legge tutte le colonne td, se la colonna esiste ed esiste il suo contenuto
	 * salva il valore nell'array, altrimenti controlla che non sia presente un immagine.
	 * Se il corso risulta essere obbligatorio, l'array conterrà 8 elementi, altrimenti 7.
	 *  
	*/
	public function parseTasse()
	{
		$response = $this->getTasse(); // Ottengo la pagina Html completa delle tasse Esse3
		
		$doc = new DOMDocument();
		$doc->validateOnParse = true;
		$doc->loadHTML($response[0]);

		$xpath = new DOMXPath($doc);
		//$table = $xpath->query("//*[@class='detail_table']")->item(0);
		$table = $doc->getElementById("tasse-tableFatt");
		// Security check
		if(empty($table))
			return null;

		$rows 		= $table->getElementsByTagName("tr");
		//$libretto 	= null;
		$tassa 		= null;

		foreach ($rows as $key => $row) {
			$cells = $row->getElementsByTagName('td');
		  	
		  	foreach ($cells as $col => $cell) {
		  		if ($cell->nodeName == 'td') {

		  			if($cell->textContent != null)
						$tassa[$key][] = $cell->textContent;

					foreach($cell->getElementsByTagName("img") as $img)
						$tassa[$key][] =  $img->getAttribute("src");
					
			 	} 
			
			} // end of foreach($cells as $cell )
			if(count($tassa[$key]) == 7)
				$tasse[$key] = new Tassa($tassa[$key]);
				
		} // end of foreach ($rows as $key => $row)
		
		return $tasse; 
	}

	/**
	 * @param f - p 
	 * @return array of Appello
	 * Questa funzione legge tutta la tabella contenente tutti gli appelli disponibili, sia totali che parziali
	 * La scelta viene fatta attraverso il parametro del metodo
	 * Legge tutte le colonne td, se la colonna esiste ed esiste il suo contenuto
	 * salva il valore nell'array, altrimenti controlla che non sia presente un immagine.
	 * Se il corso risulta essere obbligatorio, l'array 7 elementi.
	 *  
	*/
	public function parseAppelli($tipologia)
	{
		// Ottengo la pagina Html completa degli appelli Esse3
		if($tipologia == "t")
			$response = $this->getAppelli(); 
		elseif($tipologia == "p")
			$response = $this->getParziali();
		else
			return array(); // Chiamata errata, restituisco valore vuoto

		$doc = new DOMDocument();
		$doc->validateOnParse = true;
		$doc->loadHTML($response[0]);

		$xpath = new DOMXPath($doc);
		//$table = $xpath->query("//*[@class='detail_table']")->item(0);
		$table = $doc->getElementById("app-tabella_appelli");
		
		// Se non è disponibile nessun appello restituisco un valore nullo
		if(isset($table->textContent) && $table->textContent == "Nessun appello disponibile")
			return array();

		if(!isset($table->textContent))
			return array();

		$rows 		= $table->getElementsByTagName("tr");
		//$libretto 	= null;
		$esame 		= null;

		foreach ($rows as $key => $row) {
			$cells = $row->getElementsByTagName('td');
		  	
		  	foreach ($cells as $col => $cell) {
		  		if ($cell->nodeName == 'td') {

		  			if($cell->nodeValue != null)
						$esame[$key][] = $cell->nodeValue;

					// Se l'esame è prenotabile ci sarà un input quindi non sarà trovato da questo ciclo
					// Però durante il costruttore verrà settato il valore come true poiché non trova l'immagine
					foreach($cell->getElementsByTagName("img") as $img)
						$esame[$key][] =  $img->getAttribute("src");
			 	} 
			} // end of foreach($cells as $cell )
		
			if(count($esame[$key]) == 7)
				$esami[$key] = new Appello($esame[$key]);
				
		} // end of foreach ($rows as $key => $row)
			
		return $esami; 
	}

	/**
	 *  
	*/
	public function parsePrenotazioni()
	{
		
		$response = $this->getPrenotazioni(); // Ottengo la pagina Html completa delle prenotazioni Esse3

		$doc = new DOMDocument();
		$doc->validateOnParse = true;
		$doc->loadHTML($response[0]);

		$xpath 		= new DOMXPath($doc);
		$message 	= $xpath->query("//*[@class='tplMessage']")->item(1);
		
		// Se non è disponibile nessun appello restituisco un valore nullo

		if(isset($message->textContent) && $message->textContent == "Nessun appello prenotato in bacheca.")
			return array();

		$tables 		= $xpath->query("//table[@class='detail_table']");

		if(empty($tables))
			return null;

		$prenotazione = null;
		$colonnaInfo	= 5; 

		foreach($tables as $key => $table){
			$rows 		= $table->getElementsByTagName('tr');
			$prenotazione[$key] = new Prenotazione();

			foreach ($rows as $rowkey => $row) {

				$ths = $row->getElementsByTagName('th');
				$tds = $row->getElementsByTagName('td');

				if($ths->length > 0){
				  	foreach ($ths as $keyth => $th) {
				  		if ($th->nodeName == 'th') {

				  			if($th->textContent != null){
				  				if($keyth == 0 && $rowkey == 0)
									$prenotazione[$key]->setNomePrenotazione($th->textContent);
								if($keyth == 0 && $rowkey == 1)
									$prenotazione[$key]->setNumeroIscrizione($th->textContent);
								if($keyth == 0 && $rowkey == 2){
									if($th->textContent == "Giorno")
										$colonnaInfo = 4;
									else
										$prenotazione[$key]->setTipoProva($th->textContent);
								}
							}
					 	} 
					} 
				} // end if($ths->length > 0)

				if($tds->length > 0){
				  	foreach ($tds as $keytd => $td) {
				  		if ($td->nodeName == 'td') {

				  			if($td->textContent != null){
				  				if($keytd == 0 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setGiorno($td->textContent);
								if($keytd == 1 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setOra($td->textContent);
								if($keytd == 2 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setEdificio($td->textContent);
								if($keytd == 3 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setAula($td->textContent);
								if($keytd == 4 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setRiservatoPer($td->textContent);
								if($keytd == 5 && $rowkey == $colonnaInfo)
									$prenotazione[$key]->setDocenti($td->textContent);
								if($keytd == 0 && $rowkey != $colonnaInfo)
									$prenotazione[$key]->setDocenti($td->textContent);
				  			}
					 	} 
					}
				} // end if($tds->length > 0)

			} // end of foreach ($rows as $rowkey => $row)

		}	// $tables as $key => $table

		return $prenotazione;
	}

	// Restituisce l'immagine profilo dell'utente
	public function parseImmagineProfilo(){
		$response = $this->getImmagineProfilo();
		if($response[1] == 200) {
			$image = imagecreatefromstring($response[0]);
			if ($image !== false) {
				header('Content-Type: image/png');
				imagepng($image);
				imagedestroy($image);
			}
		}
	}

} // End of ParseEsse3