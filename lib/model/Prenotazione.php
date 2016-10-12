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
 
	class Prenotazione {

		private $nomePrenotazione;	// Il nome del corso a cui si è prenotati
		private $numeroIscrizione;	// Prenotazione numero x. Es: Numero Iscrizione: 45 su 69
		private $tipoProva; 		// La prova è scritta o orale? 
		private $giorno;		 	// In che data si svolge la prova
		private $ora; 				// A che ora inizia la prova
		private $edificio; 			// Edificio in cui si svolgerà l'esame
		private $aula; 				// L'aula in cui si svolgera l'esame
		private $riservatoPer; 		// Esame riservato per una determinata categoria di utenti
		private $docenti = array(); // Elenco dei docenti che supervisioneranno l'esame
		
		public function __construct(){

		}

		// Getter - Setter per nomePrenotazione
		public function setNomePrenotazione($nomePrenotazione){
			$this->nomePrenotazione = $nomePrenotazione;
		}
		public function getNomePrenotazione(){ return $this->nomePrenotazione; }

		// Getter - Setter per numeroIscrizione
		public function setNumeroIscrizione($numeroIscrizione){
			$this->numeroIscrizione = $numeroIscrizione;
		}
		public function getNumeroIscrizione(){ return $this->numeroIscrizione; }

		// Getter - Setter per tipoProva
		public function setTipoProva($tipoProva){
			$this->tipoProva = $tipoProva;
		}
		public function getTipoProva(){ return $this->tipoProva; }

		// Getter - Setter per giorno
		public function setGiorno($giorno){
			$this->giorno = $giorno;
		}
		public function getGiorno(){ return $this->giorno; }

		// Getter - Setter per ora
		public function setOra($ora){
			$this->ora = $ora;
		}
		public function getOra(){ return $this->ora; }

		// Getter - Setter per edificio
		public function setEdificio($edificio){
			$this->edificio = $edificio;
		}
		public function getEdificio(){ return $this->edificio; }

		// Getter - Setter per aula
		public function setAula($aula){
			$this->aula = $aula;
		}
		public function getAula(){ return $this->aula; }

		// Getter - Setter per riservatoPer
		public function setRiservatoPer($riservatoPer){
			$this->riservatoPer = $riservatoPer;
		}
		public function getRiservatoPer(){ return $this->riservatoPer; }

		// Getter - Setter per docenti
		public function setDocenti($docente){
			$this->docenti[] = $docente;
		}
		public function getDocenti(){ return $this->docenti; }

		// Restituisco l'oggetto sottoforma di array
		public function getPrenotazione(){
			return array(
					"nomePrenotazione" 	=> $this->nomePrenotazione 	? $this->nomePrenotazione : "",
					"numeroIscrizione" 	=> $this->numeroIscrizione 	? $this->numeroIscrizione : "",
					"tipoProva" 		=> $this->tipoProva		   	? $this->tipoProva : "",
					"giorno" 			=> $this->giorno 			? $this->giorno : "",
					"ora" 				=> $this->ora 				? $this->ora : "",
					"edificio" 			=> $this->edificio 			? $this->edificio : "",
					"aula" 				=> $this->aula				? $this->aula : "",
					"riservatoPer" 		=> $this->riservatoPer 		? $this->riservatoPer : "",
					"docenti" 			=> $this->docenti 			? $this->docenti : "",
				);
		}
	
	}