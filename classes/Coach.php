<?php

require_once  '/Utilisateur.php';

class Coach extends Utilisateur{

    private string $biographie;
    private int $experience;
    private string $photo;
    private string $certification;

    /** 
     * Les Methodes de coach
    */
    // 
    // 1- Creer / Modifier profile
    
    public function Profile(int $userid,string $biographie,int $experience,string $photo,string $certification){

        // Vérifier si profil existe
        $check = $this->pdo->prepare(
            "SELECT id FROM coach_profile WHERE user_id = ?"
        );
        $check->execute([$userId]);

        if ($check->rowCount() > 0) {
            // modifie
            $sql = "
                UPDATE coach_profile
                SET biographie = ?, experience = ?, photo = ?, certification = ?
                WHERE user_id = ?
            ";
           
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([$biographie,$experience,$photo,$certification,$userId]);
        }else{
            // creer Profile
            $sql = "
                INSERT INTO coach_profile
                (biographie, experience, photo, certification, user_id)
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([$biographie,$experience,$photo,$certification,$userId]);
        }
        
    }

    // 2- Ajouter Sports
   
    public function AjoutSport(int $coachId,array $sports){
        // Nettoyer anciennes sports
        $delete = $this->pdo->prepare(
            "DELETE FROM coach_sports where coach_id=?"
        );
        $delete->execute([$coachId]);

        // Ajouter Nouvelle Sports
        $sql ="
            insert into coach_sports(coach_id,sport_id) values (? , ?)
        ";
        $stmt =$this->pdo->prepare($sql);

        foreach($sports as $sportId){
            $stmt->execute([$coachId,$sportId]);
        }
    }

    // 3- Ajouter disponibilite de coach
    
    public function AjoutDisponibilite(int $coachId,string $date,string $heureDebut,string $heureFin) {

        $sql = "
            insert into disponibilites (coach_id, date, heure_debut,heure_fin,statut)
             values(? ,? ,? ,? ,'libre')
        ";
    
        $stmt = $this->pdo-->prepare($sql);
        
        return $stmt->execute([$coachId,$date,$heureDebut,$heureFin]);
    }

    // 4- Lister Les disponibilites 

    public function AfficherDisponibilite(int $coachId){

        $sql = "
            select * from disponibilites 
            where coach_id = ? order by date, heure_debut
        ";
        $stmt = $this->pdo->prepere($sql);
        $stmt->execute([$coachId]);

        return $stmt->fetchAll();
    }

    // 5 - Reservations 

    public function AfficherReservations(int $coachId, string $status){

        $sql = "
            SELECT r.*, u.nom, u.prenom, d.date, d.heure_debut, d.heure_fin
            FROM reservations r
            JOIN users u ON r.sportif_id = u.id
            JOIN disponibilites d ON r.availability_id = d.id
            WHERE r.coach_id = ? AND r.status = ?
            ORDER BY d.date
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$coachId, $status]);

        return $stmt->fetchAll();
    }

    // 6 - Accepter / Refuser 

    public function AcceRefuseReservation(int $reservationId,int $coachId,string $status){
        $sql = "
            UPDATE reservations
            SET status = ?
            WHERE id = ? AND coach_id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$status,$reservationId,$coachId]);
    }

}
?>