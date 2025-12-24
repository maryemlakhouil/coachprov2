<?php

require_once '/Utilisateur.php';

class Sportif extends Utilisateur{

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

    




}
?>