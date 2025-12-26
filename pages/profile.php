<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Coach.php';

// Vérification rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'coach') {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();
$coachId = $_SESSION['user']['id'];
$coach = new Coach($coachId);

// Traitement du formulaire de mise à jour
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biographie = $_POST['biographie'];
    $experience = (int)$_POST['experience'];
    $photo = $_POST['photo'] ?? '';  // tu peux adapter pour upload
    $certification = $_POST['certification'] ?? '';

    if ($coach->Profile($coachId, $biographie, $experience, $photo, $certification)) {
        $message = "Profil mis à jour avec succès ✅";
    } else {
        $message = "Erreur lors de la mise à jour ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-[#640D5F] mb-6">Mon Profil</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow space-y-4">
        <label class="block">
            Biographie
            <textarea name="biographie" class="w-full border rounded p-2"><?= htmlspecialchars($coach->getBiographie()) ?></textarea>
        </label>

        <label class="block">
            Années d'expérience
            <input type="number" name="experience" value="<?= htmlspecialchars($coach->getExperience()) ?>" class="w-full border rounded p-2">
        </label>

        <label class="block">
            Photo (URL)
            <input type="text" name="photo" value="<?= htmlspecialchars($coach->getPhoto()) ?>" class="w-full border rounded p-2">
        </label>

        <label class="block">
            Certification
            <input type="text" name="certification" value="<?= htmlspecialchars($coach->getCertification()) ?>" class="w-full border rounded p-2">
        </label>

        <button type="submit" class="bg-[#640D5F] text-white px-4 py-2 rounded hover:opacity-90">
            Mettre à jour
        </button>
    </form>
</div>

</body>
</html>
