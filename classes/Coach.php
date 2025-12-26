<?php

require_once __DIR__ . '/Utilisateur.php';

class Coach extends Utilisateur
{
    

    private string $biographie = '';
    private int $experience = 0;
    private string $photo = '';
    private string $certification = '';

    /*  CONSTRUCTEUR */

    public function __construct(int $id)
    {
        parent::__construct($id);
        $this->load();
    }

      // CHARGER PROFIL COACH

  protected function load(): bool {
    if (!parent::load()) {
        return false;
    }

    $sql = "SELECT * FROM coach_profile WHERE user_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$this->id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $this->biographie   = $data["biographie"] ?? '';
        $this->experience   = (int)($data["experience"] ?? 0);
        $this->photo        = $data["photo"] ?? '';
        $this->certification= $data["certification"] ?? '';
    } else {
        
        $sql = "INSERT INTO coach_profile (user_id, biographie, experience, photo, certification) VALUES (?, '', 0, '', '')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->id]);

        
    }

    return true;
}


   

  public function getBiographie() {
    return $this->biographie ?? '';
}

public function getExperience() {
    return $this->experience ?? 0;
}

public function getPhoto() {
    return $this->photo ?? null;
}

public function getCertification() {
    return $this->certification ?? '';
}


   
       //CRÉER / MODIFIER PROFIL
    

    public function profile(
        int $userId,
        string $biographie,
        int $experience,
        string $photo,
        string $certification
    ): bool {

        // Vérifier si le profil existe
        $check = $this->pdo->prepare(
            "SELECT id FROM coach_profile WHERE user_id = ?"
        );
        $check->execute([$userId]);

        if ($check->rowCount() > 0) {
            // Mise à jour
            $sql = "
                UPDATE coach_profile
                SET biographie = ?, experience = ?, photo = ?, certification = ?
                WHERE user_id = ?
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $biographie,
                $experience,
                $photo,
                $certification,
                $userId
            ]);
        } else {
            // Création
            $sql = "
                INSERT INTO coach_profile
                (biographie, experience, photo, certification, user_id)
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $biographie,
                $experience,
                $photo,
                $certification,
                $userId
            ]);
        }
    }

      // AJOUTER SPORTS

    public function ajoutSport(int $coachId, array $sports): void
    {
        // Supprimer anciens sports
        $delete = $this->pdo->prepare(
            "DELETE FROM coach_sports WHERE coach_id = ?"
        );
        $delete->execute([$coachId]);

        // Ajouter nouveaux sports
        $sql = "INSERT INTO coach_sports (coach_id, sport_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($sports as $sportId) {
            $stmt->execute([$coachId, $sportId]);
        }
    }

      // AJOUTER DISPONIBILITÉ
public function ajoutDisponibilite(
    int $coachId,
    string $date,
    string $heureDebut,
    string $heureFin
): bool {
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

    
       //LISTER DISPONIBILITÉS

    public function afficherDisponibilites(int $coachId): array
    {
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

    
      // RÉSERVATIONS
    

    public function afficherReservations(int $coachId, string $status): array
    {
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

       //ACCEPTER / REFUSER
   
public function accepterRefuserReservation(int $reservationId, int $coachId, string $status): bool {
    $sql = "
        UPDATE reservations
        SET status = ?
        WHERE id = ? AND coach_id = ?
    ";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$status, $reservationId, $coachId]);
}

    //   DASHBOARD STATistiques

    public function getDashboardStats(int $coachId): array
    {
        // En attente
        $pending = $this->pdo->prepare(
            "SELECT COUNT(*) FROM reservations
             WHERE coach_id = ? AND status = 'en_attente'"
        );
        $pending->execute([$coachId]);

        // Aujourd'hui
        $today = $this->pdo->prepare(
            "SELECT COUNT(*) FROM reservations r
             JOIN disponibilites d ON r.availability_id = d.id
             WHERE r.coach_id = ?
             AND r.status = 'acceptee'
             AND d.date = CURDATE()"
        );
        $today->execute([$coachId]);

        // Demain
        $tomorrow = $this->pdo->prepare(
            "SELECT COUNT(*) FROM reservations r
             JOIN disponibilites d ON r.availability_id = d.id
             WHERE r.coach_id = ?
             AND r.status = 'acceptee'
             AND d.date = CURDATE() + INTERVAL 1 DAY"
        );
        $tomorrow->execute([$coachId]);

        // Prochaine séance
        $next = $this->pdo->prepare(
            "SELECT u.nom, u.prenom, d.date, d.heure_debut, d.heure_fin
             FROM reservations r
             JOIN users u ON r.sportif_id = u.id
             JOIN disponibilites d ON r.availability_id = d.id
             WHERE r.coach_id = ?
             AND r.status = 'acceptee'
             AND d.date >= CURDATE()
             ORDER BY d.date, d.heure_debut
             LIMIT 1"
        );
        $next->execute([$coachId]);

        return [
            'pending'  => (int)$pending->fetchColumn(),
            'today'   => (int)$today->fetchColumn(),
            'tomorrow'=> (int)$tomorrow->fetchColumn(),
            'next'    => $next->fetch(PDO::FETCH_ASSOC)
        ];
    }
    public function supprimerDisponibilite(int $dispoId, int $coachId): bool{
        $sql = "DELETE FROM disponibilites WHERE id = ? AND coach_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dispoId, $coachId]);
    }



}
