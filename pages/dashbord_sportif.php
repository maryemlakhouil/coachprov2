<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Sportif.php";

if ($_SESSION['role'] !== 'sportif') {
    header("Location: login.php");
    exit;
}

$sportifId = $_SESSION['user_id'];
$sportif = new Sportif($sportifId, "", "", "");
$stats = $sportif->getDashboardStats($sportifId);
$coachs = $sportif->getCoachs();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Sportif</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 bg-[#640D5F] text-white p-6">
    <h2 class="text-2xl font-bold mb-8">Sportif</h2>
    <nav class="space-y-4">
        <a href="dashboard_sportif.php" class="block hover:text-gray-200">Dashboard</a>
        <a href="mes_reservations.php" class="block hover:text-gray-200">Mes réservations</a>
        <a href="logout.php" class="block text-red-300">Déconnexion</a>
    </nav>
</aside>

<!-- Content -->
<main class="flex-1 p-8 space-y-10">

<h1 class="text-3xl font-bold text-[#640D5F]">
    Dashboard Sportif
</h1>

<!-- Stats -->
<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded-xl p-6">
        <p class="text-gray-500">Séances réservées</p>
        <p class="text-4xl font-bold text-green-600"><?= $stats['reserved'] ?></p>
    </div>

    <div class="bg-white shadow rounded-xl p-6">
        <p class="text-gray-500">Demandes en attente</p>
        <p class="text-4xl font-bold text-yellow-600"><?= $stats['pending'] ?></p>
    </div>
</div>

<!-- Liste des coachs -->
<section>
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">
        Coachs disponibles
    </h2>

    <div class="grid md:grid-cols-3 gap-6">
        <?php foreach ($coachs as $coach): ?>
            <div class="bg-white p-6 rounded-xl shadow">
                <img
                    src="<?= $coach['photo'] ?: 'https://via.placeholder.com/150' ?>"
                    class="w-24 h-24 rounded-full mx-auto object-cover mb-4"
                >
                <h3 class="text-center font-semibold">
                    <?= htmlspecialchars($coach['prenom'].' '.$coach['nom']) ?>
                </h3>
                <p class="text-sm text-gray-600 text-center mt-2">
                    <?= htmlspecialchars(substr($coach['biographie'], 0, 80)) ?>...
                </p>
                <p class="text-center text-sm mt-2">
                    <?= $coach['experience'] ?> ans d’expérience
                </p>

                <a href="disponibilites.php?coach_id=<?= $coach['id'] ?>"
                   class="block mt-4 bg-[#640D5F] text-white text-center py-2 rounded-lg">
                    Voir disponibilités
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</main>
</body>
</html>

