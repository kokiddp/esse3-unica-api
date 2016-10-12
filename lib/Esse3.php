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
 
	class Esse3 {
		const BASEURL		= 'https://webstudenti.unica.it/esse3/';
		const HOME 			= 'https://webstudenti.unica.it/esse3/Home.do';
		const LOGIN 		= 'https://webstudenti.unica.it/esse3/auth/Logon.do';
		const CARRIERE		= 'https://webstudenti.unica.it/esse3/auth/studente/ListaCarriereStudente.do';
		const LIBRETTO 		= 'https://webstudenti.unica.it/esse3/auth/studente/Libretto/LibrettoHome.do';
		const APPELLI		= 'https://webstudenti.unica.it/esse3/auth/studente/Appelli/AppelliF.do';
		const PARZIALI		= 'https://webstudenti.unica.it/esse3/auth/studente/Appelli/AppelliP.do';
		const PRENOTAZIONI 	= 'https://webstudenti.unica.it/esse3/auth/studente/Appelli/BachecaPrenotazioni.do';
		const TASSE			= 'https://webstudenti.unica.it/esse3/auth/studente/Tasse/ListaFatture.do';
		const IMGPROFILO 	= 'https://webstudenti.unica.it/esse3/auth/AddressBook/DownloadFoto.do';
		
		private $username; 		// Username per effettuare il login
		private $password; 		// Password per effettuare il login 
		private $jsessionid; 	// Salvo il valore per il jsessionid=$jsessionid
		private $cookie;		// Il cookie che verrà settato

		private $carriere = array(); 	// Le varie carriere a disposizioe dell'utente, viene settata in ParseEsse3
		private $carrieraScelta; 		// Un elemento dell'array carriere
		private $hasCarriere; 			// Nel caso di profilo con più carriere, setto true, altrimento false

		//Inizializzo l'oggetto con i valori passati al costruttore
		public function __construct($username, $password){
			$this->username = $username;
			$this->password = $password;

			$this->initializeSession();
		}

		//Inizializza la sessione, lanciando una richiesta http ed ottenendo i cookie da settare
		private function initializeSession(){
			$ch = curl_init();//
			curl_setopt($ch, CURLOPT_URL, self::HOME);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$response = curl_exec($ch);

			//Setto il cookie ottenuto nella variabile dell'oggetto
			$this->cookie = $this->returnCookiesString($response);
			$this->setJsessionid();	
			curl_close($ch);	
		}

		/**
		 * Questa funzione viene richiamata dentro initializeSession()
		 * Prende come parametro la risposta, analizza l'header e ne ricava i cookies
		 * li concatena dentro una stringa e li restituisce. Successivamente la stringa
		 * dei cookies verrà salvata in $this->cookie
		 * @param string
		 * @return string
		 */
		private function returnCookiesString($response){
			$matches = array();

			preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);

			$cookies = array();
			// Trasformo le occorrenze in un array
			foreach($matches[1] as $item) {
			    parse_str($item, $cookie);
			    $cookies = array_merge($cookies, $cookie);
			}
			
			foreach( $cookies as $key => $value ) {
			  $arrayStrings[] = "{$key}={$value}";
			}
			$string = implode('; ', $arrayStrings);

			return $string;
		}

		// Effettuo una chiamata ad una pagina, passando l'url come parametro
		// $returnTransfer = true -> stampa a video la risposta del server
		private function requestPage($page, $returnTransfer){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $page);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
			curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
			curl_setopt($ch, CURLOPT_HEADER, false); //Non stampo l'header
			curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY); // Might need this, but I was able to verify it works without
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
			//curl_close($ch);
			$response = curl_exec($ch);
			
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			switch($httpcode){
				case '200': return array($response, $httpcode); 
					break; 
				case '400': return array(Message::get_400(), $httpcode); 
					break;
				case '401': return array(Message::get_401(), $httpcode); 
					break; 
			}

		}

		/**
		 * @return String
		 * Restituisce il valore dell'attributo $this->jsessionid
		 */
		public function getJsessionid(){
			return $this->jsessionid;
		}

		/**
		 * @return String
		 * Utilizza l'attributo $this->cookie per ricavare tramite due explode concatenati
		 * la Key del jsessionid
		 */
		private function setJsessionid(){
			$this->jsessionid = explode("=", explode(";", $this->cookie)[0])[1];
		}

		/**
		 * Setta nell'array le varie carriere disponibili per l'utente 
		 */
		public function setCarriere($carriere){
			$this->carriere = is_array($carriere) ? $carriere : null;
		}

		/**
		 * @return array
		 * Restituisce la carriera selezionata dall'utente. Se l'utente non ha più carriere, questo array sarà vuoto
		 */
		public function getCarriere(){
			return $this->carriere;
		}

		/**
		 * Setta la carriera scelta per la visualizzazione in dettaglio, prendendo un elemento dall'array $this->carriere
		 * Inoltre lancia il metodo $this->attivaCarrieraSelezionata(); per confermare al server l'attivazione della carriera
		 */
		public function setCarrieraScelta($index){

			$this->carrieraScelta = $this->carriere[$index];
			$this->attivaCarrieraSelezionata();
		}

		/**
		 * @return array
		 * Restituisce la carriera scelta selezionata precedentemente in fase di login
		 */
		public function getCarrieraScelta(){
			return $this->carrieraScelta;
		}

		public function setHasCarriere($value){
			if( in_array($value, array("true", "false", "1", "0", "yes", "no", true, false)) )
				$this->hasCarriere = $value;		
		}

		/**
		 * @return bool
		 * Restituisce true se l'utente ha più di una carriera disponibile, false altrimenti
		 */
		public function getHasCarriere(){
			return $this->hasCarriere;
		}

		/*
		 * Restituisce un campo delle varie carriere disponibili
		 * $carriera = INT è il primo indice che serve a scegliere la carriera: Es: [0]Informatica / []Corsi Singoli
		 * $colonna = INT serve a selezionare la colonna della tabella, durante il login sono presenti più link
		 * $campo = href/text serve a selezionare il volore che vogliamo utilizzare.
		 * [0] => Array (
            	[0] => Array (
                    [href] => auth/studente/SceltaCarrieraStudente.do;jsessionid=489D6943B20CAEE45693A8EE0A200DE5?stu_id=289221
                    [text] => 60/61/65101
                )
            [1] => Array (
                    [href] => auth/studente/SceltaCarrieraStudente.do;jsessionid=489D6943B20CAEE45693A8EE0A200DE5?stu_id=289221
                    [text] => Corso di Laurea
                )
        )
		 * DEPRECATA
		public function getValueCarriere($carriera, $colonna, $campo){	
			return $this->carriere[$carriera][$colonna][$campo];
		}
		*/
		

		/*
		 _   _ _____ _____ ____          ____  _____ ___  _   _ _____ ____ _____ 
		| | | |_   _|_   _|  _ \        |  _ \| ____/ _ \| | | | ____/ ___|_   _|
		| |_| | | |   | | | |_) |       | |_) |  _|| | | | | | |  _| \___ \ | |  
		|  _  | | |   | | |  __/        |  _ <| |__| |_| | |_| | |___ ___) || |  
		|_| |_| |_|   |_| |_|           |_| \_\_____\__\_\\___/|_____|____/ |_|  
		                                                                         
		*/     

		// Lancio una richiesta alla pagina di login
		public function getLogin(){
			return $this->requestPage(self::LOGIN, true);
		}

		// Se l'utente loggato ha più carriere, questa richiesta setta la carriera desiderata
		public function attivaCarrieraSelezionata(){
			return $this->requestPage( self::BASEURL . $this->carrieraScelta[0]['href'], true);
		}

		// Restituisco la pagina della carriera con le informazioni dell'utente
		public function getInfoUtente(){
			return $this->requestPage(self::HOME, true);
		}

		// Restituisco la pagina contenente il proprio libretto
		public function getLibretto(){
			return $this->requestPage(self::LIBRETTO, true);
		}
		
		// Restituisco la pagina contenente gli appelli totali disponibili
		public function getAppelli(){
			return $this->requestPage(self::APPELLI, true);
		}

		// Restituisco la pagina contenente gli appelli totali disponibili
		public function getParziali(){
			return $this->requestPage(self::PARZIALI, true);
		}

		// Restituisco la pagina contenente le prenotazioni effettuate
		public function getPrenotazioni(){
			return $this->requestPage(self::PRENOTAZIONI, true);
		}
		// Restituisco la pagina contenente le tasse
		public function getTasse(){
			return $this->requestPage(self::TASSE, true);
		}

		// Restituisco l'immagine del profilo
		public function getImmagineProfilo(){
			return $this->requestPage(self::IMGPROFILO, true);
		}

	} // end class Esse3