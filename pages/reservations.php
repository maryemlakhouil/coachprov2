<?php
session_start();
require_once "../classes/Coach.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit;
}

$coachId = (int) $_SESSION['user_id'];
$coach = new Coach($coachId);

// Gestion accepter/refuser
if (isset($_POST['action'])) {
    $reservationId = (int) $_POST['reservation_id'];
    $status = $_POST['action'] === 'accepter' ? 'acceptee' : 'refusee';
    $coach->accepterRefuserReservation($reservationId, $coachId, $status);
}

// Récupérer toutes les réservations en attente
$reservations = $coach->afficherReservations($coachId, 'en_attente');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réservations Coach</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold text-purple-700 mb-6">Réservations en attente</h1>

<div class="overflow-x-auto bg-white shadow rounded-xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">Sportif</th>
                <th class="p-3">Date</th>
                <th class="p-3">Heure</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">Aucune réservation en attente</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($reservations as $r): ?>
                <tr class="border-b">
                    <td class="p-3"><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
                    <td class="p-3"><?= $r['date'] ?></td>
                    <td class="p-3"><?= $r['heure_debut'] ?> - <?= $r['heure_fin'] ?></td>
                    <td class="p-3 space-x-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                            <button name="action" value="accepter" class="bg-green-500 text-white px-3 py-1 rounded hover:opacity-90">Accepter</button>
                        </form>
                        <form method="POST" class="inline">
                            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                            <button name="action" value="refuser" class="bg-red-500 text-white px-3 py-1 rounded hover:opacity-90">Refuser</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
