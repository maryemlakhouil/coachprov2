<?php

require_once __DIR__ . '/Utilisateur.php';


class Sportif extends Utilisateur{

    public function __construct(int $id,string $nom,string $prenom,string $email) {
        parent::__construct($id, $nom, $prenom, $email, $password, 'sportif');
    }

    /**
     * Les Methodes De Sportifs
    */

    // 1-  Lister les coaches 
    
    public function AfficherToutesCoach(){

        $sql ="
            select u.id,u.nom,u.prenom,c.biographie,c.experience,c.photo
            from users u
            join coach_profile c on u.id=c.user_id
            where u.role='coach'
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // 2 - Disponibilites d'un coach

    public function getDisponibilites(int $coachId){

        $sql = "
            select * from disponibilites 
            where coach_id = ?
            and status ='libre' and date >= curdate()
            order by date
        ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->excute([$coachId]);

        return $stmt->fetchAll();
    }

    // 3 - Reserver Une seance 
   
    public function ResrverSeance(int $sportifId,int $coachId,int $availabilityId){

        // Vérifier disponibilité
        $check = $this->pdo->prepare(
            "SELECT id FROM disponibilites
            WHERE id = ? AND statut = 'libre'"
        );
        $check->execute([$availabilityId]);

        if ($check->rowCount() === 0) {
            return false;
        }

        // Créer réservation
        $sql = "
            INSERT INTO reservations
            (sportif_id, coach_id, availability_id)
            VALUES (?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sportifId,$coachId,$availabilityId]);

        // Bloquer le créneau
        $update = $this->pdo->prepare(
            "UPDATE disponibilites
             SET statut = 'reserve' WHERE id = ?"
        );
        $update->execute([$availabilityId]);

        return true;
    }


    




}
?>