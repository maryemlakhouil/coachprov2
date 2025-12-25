<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Reservation.php';

// Sécurité
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'sportif') {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();
$reservation = new Reservation($pdo);

// Annulation
$message = '';

if (isset($_POST['annuler'])) {
    $reservationId = (int) $_POST['reservation_id'];
    $sportifId = $_SESSION['user']['id'];

    if ($reservation->annuler($reservationId, $sportifId)) {
        $message = "Réservation annulée avec succès ✅";
    } else {
        $message = "Impossible d’annuler cette réservation ❌";
    }
}

// Récupérer réservations
$reservations = $reservation->getBySportif($_SESSION['user']['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes réservations</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-[#640D5F] mb-6">
        Mes réservations
    </h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white shadow rounded-xl">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3">Coach</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Heure</th>
                    <th class="p-3">Statut</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        Aucune réservation trouvée
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($reservations as $r): ?>
                <tr class="border-b">
                    <td class="p-3">
                        <?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?>
                    </td>

                    <td class="p-3"><?= $r['date'] ?></td>

                    <td class="p-3">
                        <?= $r['heure_debut'] ?> - <?= $r['heure_fin'] ?>
                    </td>

                    <td class="p-3 font-semibold">
                        <?php
                            $statuts = [
                                'en_attente' => '🟡 En attente',
                                'acceptee'   => '🟢 Acceptée',
                                'refusee'    => '🔴 Refusée',
                                'annulee'    => '⚫ Annulée'
                            ];
                            echo $statuts[$r['status']] ?? $r['status'];
                        ?>
                    </td>

                    <td class="p-3">
                        <?php if ($r['status'] === 'en_attente'): ?>
                            <form method="POST">
                                <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                <button name="annuler"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:opacity-90">
                                    Annuler
                                </button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
