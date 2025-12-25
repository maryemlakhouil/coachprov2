
<?php
session_start();

require_once '../config/database.php';
require_once '../classes/Coach.php';
require_once '../classes/Sportif.php';
require_once '../classes/Reservation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();

$userId = $_SESSION['user_id'];
$role   = $_SESSION['role'];
$nom    = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];

$reservation = new Reservation($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-6xl mx-auto py-10 px-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-purple-600">
                Bonjour <?= htmlspecialchars($prenom) ?> 👋
            </h1>
            <p class="text-gray-600">
                Tableau de bord <?= $role ?>
            </p>
        </div>

        <a href="../auth/logout.php"
           class="bg-red-500 text-white px-4 py-2 rounded-lg">
           Déconnexion
        </a>
    </div>

    <!-- CONTENU -->
    <?php if ($role === 'sportif'): ?>

        <?php
        $reservations = $reservation->getBySportif($userId);
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Réserver -->
            <a href="coachs.php"
               class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-purple-600 mb-2">
                    Trouver un coach
                </h2>
                <p class="text-gray-600">
                    Consultez les coachs et réservez une séance.
                </p>
            </a>

            <!-- Mes réservations -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">
                    Mes réservations
                </h2>

                <?php if (empty($reservations)): ?>
                    <p class="text-gray-500">
                        Aucune réservation pour le moment.
                    </p>
                <?php else: ?>
                    <ul class="space-y-3">
                        <?php foreach ($reservations as $r): ?>
                            <li class="border-b pb-2">
                                <strong><?= $r['date'] ?></strong>
                                <?= $r['heure_debut'] ?> - <?= $r['heure_fin'] ?>
                                <br>
                                Coach : <?= $r['prenom'] ?> <?= $r['nom'] ?>
                                <span class="text-sm text-gray-500">
                                    (<?= $r['status'] ?>)
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>

    <?php else: /* COACH */ ?>

        <?php
        $reservations = $reservation->getByCoach($userId, 'en_attente');
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Gérer séances -->
            <a href="seances.php"
               class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-purple-600 mb-2">
                    Gérer mes séances
                </h2>
                <p class="text-gray-600">
                    Ajouter, modifier ou supprimer vos disponibilités.
                </p>
            </a>

            <!-- Réservations -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">
                    Réservations en attente
                </h2>

                <?php if (empty($reservations)): ?>
                    <p class="text-gray-500">
                        Aucune réservation en attente.
                    </p>
                <?php else: ?>
                    <ul class="space-y-3">
                        <?php foreach ($reservations as $r): ?>
                            <li class="border-b pb-2">
                                <strong><?= $r['date'] ?></strong>
                                <?= $r['heure_debut'] ?> - <?= $r['heure_fin'] ?>
                                <br>
                                Sportif : <?= $r['prenom'] ?> <?= $r['nom'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
