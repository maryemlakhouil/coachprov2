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

    // 4 - Afficher Mes reservations 

    public function getReservations(int $sportifId){

        $sql = "
            SELECT r.status,d.date, d.heure_debut, d.heure_fin,u.nom, u.prenom
                FROM reservations r
                JOIN disponibilites d ON r.availability_id = d.id
                JOIN users u ON r.coach_id = u.id
                WHERE r.sportif_id = ?
                ORDER BY d.date
        ";
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sportifId]);

        return $stmt->fetchAll();
    }

    // 5 - Gerer les Reservation 

    public function AnnulerReservation(int $reservationId,int $sportifId){

        // Récupérer le créneau
        $sql = "
            SELECT availability_id
            FROM reservations
            WHERE id = ? AND sportif_id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$reservationId, $sportifId]);

        $reservation = $stmt->fetch();
        if (!$reservation) {
            return false;
        }

        // Supprimer réservation

        $delete = $this->pdo->prepare(
            "DELETE FROM reservations WHERE id = ?"
        );
        $delete->execute([$reservationId]);

        // Libérer créneau ??
        $update = $this->pdo->prepare(
            "UPDATE disponibilites
             SET statut = 'libre'
             WHERE id = ?"
        );
        $update->execute([$reservation['availability_id']]);

        return true;
    }
    
}
?>