<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Seance.php';
require_once __DIR__ . '/../classes/Reservation.php';

// Vérification rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'sportif') {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();

$seance = new Seance($pdo);
$reservation = new Reservation($pdo);

// Traitement réservation
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seanceId = (int) $_POST['seance_id'];
    $coachId  = (int) $_POST['coach_id'];
    $sportifId = $_SESSION['user']['id'];

    if ($reservation->reserver($sportifId, $coachId, $seanceId)) {
        $message = "Séance réservée avec succès ";
    } else {
        $message = "Impossible de réserver cette séance ";
    }
}

// Récupérer séances disponibles
$seances = $seance->getSeancesDisponibles();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séances disponibles</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-[#640D5F] mb-6">
        Séances disponibles
    </h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-6">
        <?php foreach ($seances as $s): ?>
            <div class="bg-white shadow rounded-xl p-5">
                <p class="font-semibold text-lg">
                    Coach : <?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?>
                </p>

                <p class="text-gray-600">
                    📅 <?= $s['date'] ?>
                </p>
                <p class="text-gray-600">
                    ⏰ <?= $s['heure_debut'] ?> - <?= $s['heure_fin'] ?>
                </p>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="seance_id" value="<?= $s['id'] ?>">
                    <input type="hidden" name="coach_id" value="<?= $s['coach_id'] ?>">

                    <button class="bg-[#640D5F] text-white px-4 py-2 rounded hover:opacity-90">
                        Réserver
                    </button>
                </form>
            </div>
        <?php endforeach; ?>

        <?php if (empty($seances)): ?>
            <p class="text-gray-500">Aucune séance disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
