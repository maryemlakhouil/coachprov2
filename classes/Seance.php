<?php
    class Seance{

        private PDO $pdo;
        private int $id;
        private int $coachId;
        private string $date;
        private string $heureDebut;
        private string $heureFin;
        private string $status;

        public function __construct(){
            $pdo=Database::getConnection();
        }

        /**
         * Les Methodes De Seance
        */
        // 1. AJOUTER UNE SÉANCE

        public function ajouterSeance(int $coachId,string $date,string $heureDebut,string $heureFin){
            
        $sql = "
            INSERT INTO disponibilites
            (coach_id, date, heure_debut, heure_fin, status)
            VALUES (?, ?, ?, ?, 'libre')
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $coachId,
            $date,
            $heureDebut,
            $heureFin
        ]);
    }

    }
?>
