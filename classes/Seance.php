<?php
    class Seance{

        private PDO $pdo;
        private int $id;
        private int $coachId;
        private string $date;
        private string $heureDebut;
        private string $heureFin;
        private string $status;

      
        public function __construct(PDO $pdo){
             $this->pdo = $pdo;
        }

        /**
         * Les getters et Les setters 
        */

        public function getDate(){
            return $this->date;
        }

        public function getHeureDebut(){
            return $this->heureDebut;
        }

        public function getHeureFin(){
            return $this->heureFin;
        }

        public function getId(){
            return $this->id;
        }

        public function getCoachid(){
            return $this->coachId;
        }

        public function getStatus(){
            return $this->status;
        }

        public function setStatus(string $status){
            $this->status=$status;
        }
       
        /**
         * Les Methodes De la classe Seance
        */
        // 1. AJOUTER UNE SÉANCE

        public function ajouterSeance(int $coachId,string $date,string $heureDebut,string $heureFin){

            $sql = "
                INSERT INTO disponibilites
                (coach_id, date, heure_debut, heure_fin, status)
                VALUES (?, ?, ?, ?, 'libre')
            ";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$coachId,$date,$heureDebut,$heureFin]);
        }

        // 2 - Modifier une seance 

        public function modifierSeance(int $seanceId,int $coachId,string $date,string $heureDebut,string $heureFin){

            $sql = "
                UPDATE disponibilites
                SET date = ?, heure_debut = ?, heure_fin = ?
                WHERE id = ? AND coach_id = ?
            ";
            $stmt =$this->pdo->prpare($sql);
            return $stmt->excute([$date,$heureDebut,$heureFin,$seanceId,$coachId]);
        }

        // 3 - Supprimer une seance 

        public function supprimerSeance(int $seanceId,int $coachId){

            $sql = "
                DELETE FROM disponibilites
                WHERE id = ? AND coach_id = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$seanceId, $coachId]);
        }

        // 4- get seance par coach 

        public function getSeanceParCoach(int $coachId){
            
            $sql = "
                SELECT *
                FROM disponibilites
                WHERE coach_id = ?
                ORDER BY date, heure_debut
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$coachId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 5 - Tous les Sances Disponibles

        public function getSeancesDisponibles(){

            $sql = "
                SELECT d.*, u.nom, u.prenom
                FROM disponibilites d
                JOIN users u ON d.coach_id = u.id
                WHERE d.status = 'libre'    
                AND d.date >= CURDATE()
                ORDER BY d.date, d.heure_debut
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } 
    }
?>
