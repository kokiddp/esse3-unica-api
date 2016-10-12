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

			    
			   	if($title == 'Home Studente'){
			   		$this->setHasCarriere(false);
			   	}

			   	if($title == 'Area Studente'){

					$xpath = new DOMXpath($doc);
					$articles = $xpath->query('//table[@class="detail_table"]');
					 	$links = array();
						
						foreach($articles as $container) {
					    	$arr = $container->getElementsByTagName("a");
					    	
					    	foreach($arr as $item) {
							    $href =  $item->getAttribute("href");
							    $text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
							    $links[] = array( 'href' => $href, 'text' => $text );
					    	}
					  	}
					$this->setHasCarriere(true);
					$this->setCarriere( array_chunk($links, 4) );
				}
				
				return Message::get_200();
        	}  // endif status == 200

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

			    
			
			if($title == 'Home Utente Registrato'){
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
				$utente->setMatricola($matricola->nodeValue);
				$utente->setStatoCarriera("registrata");
				return $utente->getUtente();
			}

			// Provo a leggere l'html leggendo i vari id e tag della pagina. Alcune volte la lettura fallisce
			// per questo utilizzo un try catch
			if($title == 'Home Studente'){ 
				$datiPersonaliHtml = $doc->getElementById("gu-homepagestudente-cp1Child");
				
				if(empty($datiPersonaliHtml)){ // Se è vuoto c'è un problema, restituisco null per sicurezza
					$utente = new Utente();
					return $utente->getUtente(); // Sarà vuoto
				}
				
				$ddHtml = $datiPersonaliHtml->getElementsByTagName("dd");
				$matricola = $doc->getElementById("gu-header");

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
				$utente->setEmailAteneo($datiPersonali[5]['description'][0]['#text']);
				$utente->setTelefono($datiPersonali[6]['description'][0]['#text']);		
				$utente->setMatricola($matricola->nodeValue);
				$utente->setStatoCarriera("attiva");
				return $utente->getUtente();
			}
        }

        /**
         * @return Array
         * @link http://stackoverflow.com/questions/12803228/simple-html-dom-how-get-dt-dd-elements-to-array
         * Parsa il valore ottenuto dalla home page per restituire un array di valori dal tag "dd"
         */
		private function getArrayForParseUtente($node) { 
			$array = false; 

			if ($node->hasAttributes()) { 
				foreach ($node->attributes as $attr) { 
		    		$array[$attr->nodeName] = $attr->nodeValue; 
				} 
			} 

			if ($node->hasChildNodes()) { 
				if ($node->childNodes->length == 1) { 
		    		$array[$node->firstChild->nodeName] = $node->firstChild->nodeValue; 
				} else { 
		    		foreach ($node->childNodes as $childNode) { 
		        		if ($childNode->nodeType != XML_TEXT_NODE) { 
		            	$array[$childNode->nodeName][] = $this->getArrayForParseUtente($childNode); 
		        		} 
		    		} 
				} 
			} 
			return $array; 
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
			elseif($table->nodeValue == "Non è stata ancora sostenuta alcuna attività didattica.")
				return null;

			$rows 		= $table->getElementsByTagName("tr");
			//$libretto 	= null;
			$esami 		= null;

			/*
			foreach ($rows as $key => $row) {
				$cells = $row->getElementsByTagName('td');
			  	
			  	foreach ($cells as $col => $cell) {
			  		if ($cell->nodeName == 'td') {

			  			if($cell->nodeValue != null)
							$esami[$key][] = $cell->nodeValue;

						foreach($cell->getElementsByTagName("img") as $img)
							$esami[$key][] =  $img->getAttribute("src");
						
				 	} 
				
				} // end of foreach($cells as $cell )

				$libretto[$key] = new Libretto($esami[$key]);
			} // end of foreach ($rows as $key => $row)
			*/

			foreach ($rows as $key => $row) {
				$cells = $row->getElementsByTagName('td');
				
				if($cells->length == 13){
					foreach ($cells as $col => $cell) {
						//echo '<pre>'; print_r($cell);
				  		if ($cell->nodeName == 'td') {

				  			if($cell->nodeValue != null)
								$esami[$key][] = $cell->nodeValue;

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
				  			if($cell->nodeValue != null && $col != 0)
								$esami[$key][] = $cell->nodeValue;

							foreach($cell->getElementsByTagName("img") as $img)
								$esami[$key][] =  $img->getAttribute("src") == "images/figlio_raggr.gif" ? $esami[$key-1][0] : $img->getAttribute("src"); //echo $img->getAttribute("src") .'<br>'; 

							foreach($cell->getElementsByTagName("a") as $img)
								$esami[$key][] =  $img->nodeValue; //echo $img->nodeValue.'<br>';
							
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
			$table = $xpath->query("//*[@class='detail_table']")->item(0);

			$rows 		= $table->getElementsByTagName("tr");
			//$libretto 	= null;
			$tassa 		= null;

			foreach ($rows as $key => $row) {
				$cells = $row->getElementsByTagName('td');
			  	
			  	foreach ($cells as $col => $cell) {
			  		if ($cell->nodeName == 'td') {

			  			if($cell->nodeValue != null)
							$tassa[$key][] = $cell->nodeValue;

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
			$table = $xpath->query("//*[@class='detail_table']")->item(0);
			
			// Se non è disponibile nessun appello restituisco un valore nullo
			if(isset($table->nodeValue) && $table->nodeValue == "Nessun appello disponibile")
				return array();

			if(!isset($table->nodeValue))
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

			if(isset($message->nodeValue) && $message->nodeValue == "Nessun appello prenotato in bacheca.")
				return array();

			$tables 		= $xpath->query("//table[@class='detail_table']");
			$prenotazione[] = null;
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

					  			if($th->nodeValue != null){
					  				if($keyth == 0 && $rowkey == 0)
										$prenotazione[$key]->setNomePrenotazione($th->nodeValue);
									if($keyth == 0 && $rowkey == 1)
										$prenotazione[$key]->setNumeroIscrizione($th->nodeValue);
									if($keyth == 0 && $rowkey == 2){
										if($th->nodeValue == "Giorno")
											$colonnaInfo = 4;
										else
											$prenotazione[$key]->setTipoProva($th->nodeValue);
									}
								}
						 	} 
						} 
					} // end if($ths->length > 0)

					if($tds->length > 0){
					  	foreach ($tds as $keytd => $td) {
					  		if ($td->nodeName == 'td') {

					  			if($td->nodeValue != null){
					  				if($keytd == 0 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setGiorno($td->nodeValue);
									if($keytd == 1 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setOra($td->nodeValue);
									if($keytd == 2 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setEdificio($td->nodeValue);
									if($keytd == 3 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setAula($td->nodeValue);
									if($keytd == 4 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setRiservatoPer($td->nodeValue);
									if($keytd == 5 && $rowkey == $colonnaInfo)
										$prenotazione[$key]->setDocenti($td->nodeValue);
									if($keytd == 0 && $rowkey != $colonnaInfo)
										$prenotazione[$key]->setDocenti($td->nodeValue);
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