
<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Coach.php";

// Vérification session et rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header("Location: login.php");
    exit;
}

// Vérifier la clé user id
$coachId = $_SESSION['user_id'];  
$coach = new Coach($coachId);
$coach = new Coach($coachId);

if (empty($coach->getBiographie()) ||$coach->getExperience() === 0) {
    header('Location: completer_coach.php');
    exit;
}



$stats = $coach->getDashboardStats($coachId);

// S'assurer que les clés existent
$stats = array_merge(['pending' => 0,'today' => 0,'tomorrow' => 0,'next' => null], $stats);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coach->ajoutDisponibilite(
        $coachId,
        $_POST['date'],
        $_POST['heure_debut'],
        $_POST['heure_fin']
    );
}

/* DELETE */

if (isset($_GET['delete_dispo'])) {
    $coach->supprimerDisponibilite(
        (int) $_GET['delete_dispo'],
        $coachId
    );
}


$dispos = $coach->afficherDisponibilites($coachId);
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
                <a href="../pages/dashbord_coach.php" class="block hover:text-gray-200">
                    Dashboard
                </a>

                <a href="../pages/profile.php" class="block hover:text-gray-200">
                    Mon Profile 
                </a>
                <a href="../pages/reservations.php" class="block hover:text-gray-200">
                    Gerer reservations
                </a>

                <a href="../pages/logout.php" class="block text-red-300 hover:text-red-400">
                    Déconnexion
                </a>
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
        <section class="bg-white rounded-xl shadow p-6">

<h2 class="text-2xl font-bold text-[#640D5F] mb-4">
     Mes disponibilités
</h2>

<!-- FORM -->
<form method="POST" class="grid md:grid-cols-3 gap-4 mb-6">

    <input type="hidden" name="add_dispo">

    <div>
        <label class="font-semibold">Date</label>
        <input type="date" name="date" required
               class="w-full border rounded-lg p-3">
    </div>

    <div>
        <label class="font-semibold">Heure début</label>
        <input type="time" name="heure_debut" required
               class="w-full border rounded-lg p-3">
    </div>

    <div>
        <label class="font-semibold">Heure fin</label>
        <input type="time" name="heure_fin" required
               class="w-full border rounded-lg p-3">
    </div>

    <div class="md:col-span-3">
        <button class="bg-[#640D5F] text-white px-6 py-2 rounded-lg">
            Ajouter disponibilité
        </button>
    </div>
</form>

<!-- LIST -->
<table class="w-full text-sm border-collapse">
<thead>
<tr class="bg-gray-100">
    <th class="p-2">Date</th>
    <th class="p-2">Début</th>
    <th class="p-2">Fin</th>
    <th class="p-2">Statut</th>
    <th class="p-2">Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($dispos as $d): ?>
<tr class="text-center">
    <td class="border p-2"><?= $d['date'] ?></td>
    <td class="border p-2"><?= $d['heure_debut'] ?></td>
    <td class="border p-2"><?= $d['heure_fin'] ?></td>
    <td class="border p-2"><?= $d['status'] ?></td>
    <td class="border p-2">
        <?php if ($d['status'] === 'libre'): ?>
            <a href="?delete_dispo=<?= $d['id'] ?>"
               class="text-red-500 hover:underline">
                Supprimer
            </a>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</section>

</main>
</body>
</html>
