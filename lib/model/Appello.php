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

	class Appello {

		private $prenotabile;		// Il corso è già prenotabile?
		private $nomeCorso; 		// Il nome del corso
		private $dataAppello; 		// In che data si svolge l'esame
		private $dataIscrizione; 	// Data apertura e chiusura delle scrizioni all'esame
		private $descrizione; 		// Descrizione dell'esame 
		private $presidente; 		// Presidente dell'esame 
		private $cfu; 				// Quanti CFU vale il corso
		
		public function __construct($appello){
			if(count($appello) == 7){
				$this->setPrenotabile($appello[0]);
				$this->setNomeCorso($appello[1]);
				$this->setDataAppello($appello[2]);
				$this->setDataIscrizione($appello[3]);
				$this->setDescrizione($appello[4]);
				$this->setPresidente($appello[5]);
				$this->setCfu($appello[6]);

			}
		}

		// Getter - Setter per prenotabile
		public function setPrenotabile($prenotabile){
			if($prenotabile == "images/app_no_pren.gif")
				$this->prenotabile = "false";
			else
				$this->prenotabile = "true";

		}
		public function getPrenotabile(){ return $this->prenotabile; }
		
		// Getter - Setter per nomeCorso
		public function setNomeCorso($nomeCorso){
			$this->nomeCorso = ucfirst(strtolower($nomeCorso));
		}
		public function getNomeCorso(){ return $this->nomeCorso; }
		
		// Getter - Setter per dataAppello
		public function setDataAppello($dataAppello){
			$this->dataAppello = $dataAppello;
		}
		public function getDataAppello(){ return $this->dataAppello; }
		
		// Getter - Setter per dataIscrizione
		public function setDataIscrizione($dataIscrizione){
			$this->dataIscrizione = $dataIscrizione;
		}
		public function getDataIscrizione(){ return $this->dataIscrizione; }
		
		// Getter - Setter per descrizione
		public function setDescrizione($descrizione){
			$this->descrizione = $descrizione; 
		}
		public function getDescrizione(){ return $this->descrizione; }

		// Getter - Setter per presidente
		public function setPresidente($presidente){
			$this->presidente = $presidente;
		}
		public function getPresidente(){ return $this->presidente; }
		
		// Getter - Setter per cfu
		public function setCfu($cfu){
			$this->cfu = $cfu;
		}
		public function getCfu(){ return $this->cfu; }

		// Restituisco l'oggetto sottoforma di array
		public function getAppello(){
			return array(
					"prenotabile" 		=> $this->prenotabile 	? $this->prenotabile : "",
					"nomeCorso" 		=> $this->nomeCorso 	? $this->nomeCorso : "",
					"dataAppello" 		=> $this->dataAppello 	? $this->dataAppello : "",
					"dataIscrizione" 	=> $this->dataIscrizione ? $this->dataIscrizione : "",
					"descrizione" 		=> $this->descrizione 	? $this->descrizione : "",
					"presidente" 		=> $this->presidente 	? $this->presidente : "",
					"cfu" 				=> $this->cfu 			? $this->cfu : ""
				);
		}
	
	}