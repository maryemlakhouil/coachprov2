<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Coach.php";

if ($_SESSION['role'] !== 'coach') {
    header("Location: login.php");
    exit;
}

$coachId = $_SESSION['user_id'];
$coach = new Coach($coachId, "", "", "");
$stats = $coach->getDashboardStats($coachId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Coach</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 bg-purple-700 text-white p-6">
    <h2 class="text-2xl font-bold mb-8">Coach Panel</h2>
    <nav class="space-y-4">
        <a href="dashboard_coach.php" class="block hover:text-gray-200">Dashboard</a>
        <a href="profile.php" class="block hover:text-gray-200">Mon Profil</a>
        <a href="seances.php" class="block hover:text-gray-200">Mes Séances</a>
        <a href="reservations.php" class="block hover:text-gray-200">Réservations</a>
        <a href="logout.php" class="block text-red-200">Déconnexion</a>
    </nav>
</aside>

<!-- Content -->
<main class="flex-1 p-8 space-y-8">

<h1 class="text-3xl font-bold text-purple-700">
    Dashboard Coach
</h1>

<!-- Stats -->
<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">Demandes en attente</p>
        <p class="text-4xl font-bold text-purple-600"><?= $stats['pending'] ?></p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">Séances aujourd'hui</p>
        <p class="text-4xl font-bold text-green-600"><?= $stats['today'] ?></p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">Séances demain</p>
        <p class="text-4xl font-bold text-blue-600"><?= $stats['tomorrow'] ?></p>
    </div>
</div>

<!-- Prochaine séance -->
<div class="bg-white p-6 rounded-xl shadow">
    <h2 class="text-xl font-semibold mb-4">Prochaine séance</h2>

    <?php if ($stats['next']): ?>
        <p class="text-gray-700">
            <?= htmlspecialchars($stats['next']['prenom']) ?>
            <?= htmlspecialchars($stats['next']['nom']) ?> —
            <?= $stats['next']['date'] ?> |
            <?= $stats['next']['heure_debut'] ?> - <?= $stats['next']['heure_fin'] ?>
        </p>
    <?php else: ?>
        <p class="text-gray-500">Aucune séance planifiée</p>
    <?php endif; ?>
</div>

</main>
</body>
</html>
