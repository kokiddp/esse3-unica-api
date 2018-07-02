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
 
	class Tassa {

		private $idPagamento; 				// L'ID del pagamento 
		private $numeroAvvisoPagamento; 	// Numero del pagamento (? simile all'id)
		//private $anno; 						// Anno di riferimento
		private $descrizione; 				// Descrizione relativa al pagamento (es: Tassa di iscrizione/immatricolazione)
		private $scadenza; 					// Quando scade il termine ultimo per il pagamento
		private $importo; 					// Importo del pagamento
		private $stato; 					// Booleano, da pagare o pagato
		
		/**
		 * In ParseEsse3 quando viene richiamato un costruttore viene
		 * passato un array così strutturato
		 *  [2] => Array
			        (
			            [0] => 5298710
			            [1] => 201611315365
			            [2] => 15/16
			            [3] => Imposta di bollo per domanda online
			            [4] => 30/09/2016
			            [5] => € 57,63
			            [6] => images/semaf_v.gif
			        )   
	     */
		public function __construct($tassa){

			if(count($tassa) == 7){
				$this->setIdPagamento($tassa[0]);
				$this->setNumeroAvvisoPagamento($tassa[1]);
				//$this->setAnno($tassa[2]);
				$this->setDescrizione($tassa[2]);
				$this->setScadenza($tassa[3]);
				$this->setImporto($tassa[4]);
				$this->setStato($tassa[5]);

			}
		}

		// Getter - Setter per idPagamento
		public function setIdPagamento($idPagamento){
			$this->idPagamento = $idPagamento;
		}
		public function getIdPagamento(){ return $this->idPagamento; }

		// Getter - Setter per numeroAvvisoPagamento
		public function setNumeroAvvisoPagamento($numeroAvvisoPagamento){
			$this->numeroAvvisoPagamento = $numeroAvvisoPagamento;
		}
		public function getNumeroAvvisoPagamento(){ return $this->numeroAvvisoPagamento; }

		// Getter - Setter per setAnno
		public function setAnno($anno){
			$this->anno = $anno;
		}
		public function getAnno(){ return $this->anno; }

		// Getter - Setter per descrizione
		public function setDescrizione($descrizione){
			$this->descrizione = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $descrizione));
		}
		public function getDescrizione(){ return $this->descrizione; }

		// Getter - Setter per scadenza
		public function setScadenza($scadenza){
			$this->scadenza = $scadenza;
		}
		public function getScadenza(){ return $this->scadenza; }

		// Getter - Setter per importo
		public function setImporto($importo){
			$this->importo = $importo;
		}
		public function getImporto(){ return $this->importo; }

		// Getter - Setter per stato
		public function setStato($stato){
			switch($stato){
				case 'images/semaf_v.gif': 
					$this->stato = 'Pagata';
				break;

				case 'images/semaf_g.gif': 
					$this->stato = 'In corso';
				break;

				case 'images/semaf_r.gif': 
					$this->stato = 'Da pagare';
				break;
			}

		}
		public function getStato(){ return $this->stato; }

		// Restituisco l'oggetto sottoforma di array
		public function getTassa(){
			return array(
					"idPagamento" 			=> $this->idPagamento 			? $this->idPagamento : "",
					"numeroAvvisoPagamento" => $this->numeroAvvisoPagamento ? $this->numeroAvvisoPagamento : "", 
					//"anno" 					=> $this->anno 					? $this->anno : "", 
					"descrizione"			=> $this->descrizione 			? $this->descrizione : "",
					"scadenza" 				=> $this->scadenza 				? $this->scadenza : "",
					"importo" 				=> $this->importo 				? $this->importo : "",
					"stato" 				=> $this->stato 				? $this->stato : ""
				);
		}
	}