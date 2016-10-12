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
 
	class Libretto {

		private $annoCorso; 		// L'anno a cui appartiene il corso
		private $nomeCorso; 		// Il nome del corso
		private $crediti; 			// Quanti crediti da il corso
		private $stato; 			// Esame superato o ancora da sostenere?
		private $annoFrequenza; 	// In che anno è stato frequentato il corso
		private $voto; 				// Con che voto è stato superato l'esame
		private $lode; 				// Il voto ha la lode?
		private $dataConvalida;		// In che data è stato convalidato l'esame
		private $obbligatorio;		// L'esame in questione è collegato al piano di studi?

		/**
		 * In ParseEsse3 quando viene richiamato un costruttore viene
		 * passato un array così strutturato
		 *  [8] => Array(
		            [0] => 1
		            [1] => 60/61/117 - ALGORITMI E STRUTTURE DATI 1
		            [2] => images/ad_piano.gif
		            [3] => 9
		            [4] => images/superata.gif
		            [5] => 2014/2015
		            [6] => 23 - 13/01/2015
		            [7] => images/detail.jpg)

		    [7] => Array(
		            [0] => 1
		            [1] => 60/61/130 - AMMINISTRAZIONE DI SISTEMA
		            [2] => 6
		            [3] => images/superata.gif
		            [4] => 2014/2015
		            [5] => 26 - 15/06/2015
		            [6] => images/detail.jpg)

		     [9] => Array(
		            [0] => 1
		            [1] => 60/61/130 - AMMINISTRAZIONE DI SISTEMA
		            [2] => 6
		            [3] => images/superata.gif
		            [4] => 2014/2015
		            [5] => 26 - 15/06/2015
		            [6] => images/detail.jpg)       
	     */
		public function __construct($corso){
			if(count($corso) != 0){
				
				//
				if(count($corso) == 7){
					$this->setAnnoCorso($corso[0]);
					$this->setNomeCorso("$corso[1]");
					$this->setCrediti($corso[2]);
					$this->setStato($corso[3]);
					$this->setAnnoFrequenza($corso[4]);
					$this->setVoto($corso[5], $corso[3]);
					$this->setDataConvalida($corso[5], $corso[3]);
					$this->setObbligatorio($corso[2]);
				}
				if(count($corso) == 8 || count($corso) == 9){
					$this->setAnnoCorso($corso[0]);
					$this->setNomeCorso($corso[1]);
					$this->setCrediti($corso[3]);
					$this->setStato($corso[4]);
					$this->setAnnoFrequenza($corso[5]);
					$this->setVoto($corso[6], $corso[4]);
					$this->setDataConvalida($corso[6], $corso[4]);
					$this->setObbligatorio($corso[2]);
				}

			}
		}

		// Getter - Setter per annoCorso
		private function setAnnoCorso($annoCorso){
			$this->annoCorso = $annoCorso;
		}
		public function getAnnoCorso(){ return $this->annoCorso; }

		// Getter - Setter per nomeCorso
		private function setNomeCorso($nomeCorso){
			$this->nomeCorso = $nomeCorso;
		}
		public function getNomeCorso(){ return $this->nomeCorso; }

		// Getter - Setter per crediti
		private function setCrediti($crediti){
			if($crediti != "images/ad_piano.gif")
				$this->crediti = (int) $crediti;
			elseif($crediti == "images/ad_piano.gif")
				$this->crediti = "";
		}

		public function getCrediti(){ return $this->crediti; }

		// Getter - Setter per stato
		private function setStato($stato){
			switch($stato){
				case 'images/pianificata.gif': 
					$this->stato = 'Pianificato';
				break;
				case 'images/frequentata.gif': 
					$this->stato = 'Frequentato';
				break;
				case 'images/superata.gif': 
					$this->stato = 'Superato';
				break;
			}
		}

		public function getStato(){ return $this->stato; }

		// Getter - Setter per annoFrequenza
		private function setAnnoFrequenza($annoFrequenza){
			$this->annoFrequenza = $annoFrequenza;
		}

		public function getAnnoFrequenza(){ return $this->annoFrequenza; }

		// Getter - Setter per voto
		private function setVoto($voto, $stato){
			// 23 - 13/01/2015 (voto - anno convalida)
			if($stato == 'images/superata.gif'){
				$voto = trim(explode("-", $voto)[0]);
				if($voto == "30L "){
					$this->voto = "30";
					$this->lode = true;
				}
				else{
					$this->voto = str_replace( chr( 194 ) . chr( 160 ), '', $voto ); // pulisco la stringa dal carattere \u00a0 
					$this->lode = false;
				}
			}
		}

		public function getVoto(){ return $this->voto; }

		// Getter - Setter per dataConvalida
		private function setDataConvalida($dataConvalida, $stato){
			// 23 - 13/01/2015 (voto - anno convalida)
			if($stato == 'images/superata.gif')
				$this->dataConvalida = trim(explode("-", $dataConvalida)[1]);
		}

		public function getDataConvalida(){ return $this->data; }

		// Getter - Setter per obbligatorio
		private function setObbligatorio($obbligatorio){
			if($obbligatorio == "images/ad_piano.gif")
				$this->obbligatorio = "Obbligatorio";
			else
				$this->obbligatorio = "Esame a scelta";
		}

		public function getObbligatorio(){ return $this->obbligatorio; }

		// Restituisco l'oggetto sottoforma di array
		public function getLibretto(){

			return array(
					"nomeCorso" 	=> $this->nomeCorso 	? $this->nomeCorso : "",
					"annoCorso" 	=> $this->annoCorso 	? $this->annoCorso : "", 
					"crediti" 		=> $this->crediti 		? $this->crediti : "", 
					"stato"			=> $this->stato 		? $this->stato : "",
					"annoFrequenza" => $this->annoFrequenza ? $this->annoFrequenza : "",
					"obbligatorio" 	=> $this->obbligatorio 	? $this->obbligatorio : "",
					"voto" 			=> $this->voto 			? $this->voto : "",
					"lode" 			=> $this->lode 			? $this->lode : "",
					"dataConvalida" => $this->dataConvalida ? $this->dataConvalida : "",
				);
		}
	}