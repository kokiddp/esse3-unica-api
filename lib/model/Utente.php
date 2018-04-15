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
 
	class Utente {
		private $nome = "";			// Nome dell'utente loggato
		private $cognome = ""; 		// Cognome dell'utente loggato
		private $matricola = ""; 	// Matricola nel formato xx/yy/jjjj
		private $email = ""; 		// Email dell'utente
		private $emailAteneo = "";	// Email dell'ateneo associata all'utente
		private $telefono = "";		// Telefono dell'utente
		private $statoCarriera = ""; // Lo stato della carriera
		private $cfuTotali = 0; 	// CFU totali attualmente disponibili
		private $cfu = 0; 			// CFU attualmente convalidati
		private $mediaAritmetica = 0; // Media artmetica degli esami
		private $mediaPonderata = 0; // Media ponderata degli esami

		//private $domicilio;
		//private $residenza;

		/*
		* Setter e Getter per i vari attributi
		*/

		// Metto il primo carattere maiuscolo, tutti gli altri minuscoli, facendo l'explode del nome 
		// utilizzando chr(0xC2).chr(0xA0) per parsare il &nbsp;
		public function setNome($nome) { 			
			$this->nome = ucfirst( strtolower( explode(chr(0xC2).chr(0xA0), $nome)[0])); 
		}

		public function getNome() { return $this->nome;}

		// Stesso criterio per setNome
		public function setCognome($cognome){ 
			$this->cognome = ucfirst( strtolower( explode(chr(0xC2).chr(0xA0), $cognome)[1]));  
		}
		public function getCognome() { return $this->cognome; }

		// A questo attributo arriva una stringa di benvenuto contenente pure la matricola. Uso explode per ricavarla
		public function setMatricola($m){ 
			$this->matricola = explode(")" , explode(". ", $m)[1])[0] ;
		}
		/*
		 * Se complete è true restituisce tutta la matricola completa, altrimenti solo le ultime 5 cifre
		 * 60/61/65010
		 * 65010
		 */
		public function getMatricola($complete) 
		{
			if(!$this->matricola)
				return  ""; 
			
			if($complete == true) 
				return $this->matricola;
			else 
				return explode("/", $this->matricola)[2];
		}

		// Getter - Setter per email
		public function setEmail($email){ 
			$this->email = $email; 
		}
		public function getEmail() { return $this->email; }

		// Getter - Setter per emailAteneo
		public function setEmailAteneo($emailAteneo){ 
			$this->emailAteneo = $emailAteneo; 
		}
		public function getEmailAteneo() { return $this->emailAteneo;}

		// Getter - Setter per telefono
		public function setTelefono($telefono){ 
			$this->telefono = explode("(", $telefono)[0]; 
		}
		public function getTelefono() { return $this->telefono;}

		// Getter - Setter per statoCarriera
		public function setStatoCarriera($statoCarriera){
			if($statoCarriera == "registrata")
				$this->statoCarriera = "registrata"; // La carriera potrebe essere in fase di perfezionamento
			elseif($statoCarriera == "attiva")
				$this->statoCarriera = "attiva"; // l'utente ha la carriera attivata correttamente
			else
				$this->statoCarriera = "";
		}
		public function getStatoCarriera(){ return $this->statoCarriera;}

		// Restituisco l'oggetto sottoforma di array
		public function getUtente(){
			return array(
					"nome" 			=> $this->nome,
					"cognome" 		=> $this->cognome, 
					"matricolaL" 	=> $this->matricola, // Matricola nel formato xx/yy/jjjj
					"matricolaS"	=> $this->getMatricola(false),
					"email" 		=> $this->email,
					"emailAteneo" 	=> $this->emailAteneo,
					"telefono" 		=> $this->telefono,
					"statoCarriera" => $this->statoCarriera,
					"cfu" 			=> $this->cfu ,
					"cfuTotali" 	=> $this->cfuTotali ,
					"mediaAritmetica" => $this->mediaAritmetica,
					"mediaPonderata" 	=> $this->mediaPonderata,
				);
		}

		/*
		public setDomicilio($d){ $this->domicilio = $d}
		public getDomicilio() { return $this->domicilio;}

		public setResidenza($r){ $this->residenza = $r}
		public getResidenza() { return $this->residenza;}
		*/

		public function setCfuAndMedia($librettoArray){

			if(is_array($librettoArray)){
				foreach($librettoArray as $l)
					$libretto[] = $l->getLibretto();
			}

			$esamiSuperati = 0;
			$sommaVoti = 0;
			$sommaPonderata = 0;
			$cfuMedia = 0;
			
			foreach($libretto as $l){
				if($l['stato'] == "Superato"){
					$esamiSuperati++;
					// Escludo le idoneità
					if($l['voto'] != "IDO"){
						$sommaPonderata += $l['voto'] * $l['crediti'];
						$sommaVoti += $l['voto'];
						$cfuMedia += $l['crediti'];
					}
					$this->cfu += $l['crediti'];
				}

				$this->cfuTotali += $l['crediti'];
			}

			$this->mediaAritmetica = $sommaVoti / $esamiSuperati;
			$this->mediaPonderata = $sommaPonderata / $cfuMedia;
		}
	}