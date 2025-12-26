<?php
session_start();
require_once __DIR__ . '/../classes/Coach.php';


  // AUTH CHECK

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit;
}

$coachId = (int) $_SESSION['user_id'];
$coach = new Coach($coachId);

$message = '';


   //FORM SUBMIT

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $biographie    = trim($_POST['biographie']);
    $experience    = (int) $_POST['experience'];
    $photo         = trim($_POST['photo']);
    $certification = trim($_POST['certification']);

    if ($coach->profile(
        $coachId,
        $biographie,
        $experience,
        $photo,
        $certification
    )) {
        header('Location: dashbord_coach.php');
        exit;
    } else {
        $message = "Erreur lors de l'enregistrement du profil";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compléter mon profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-3xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-[#640D5F] mb-6">
        Compléter mon profil
    </h1>

    <?php if ($message): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow space-y-4">

        <div>
            <label class="font-semibold">Biographie</label>
            <textarea name="biographie" required
                class="w-full border rounded-lg p-3"
                rows="4"><?= htmlspecialchars($coach->getBiographie()) ?></textarea>
        </div>

        <div>
            <label class="font-semibold">Années d'expérience</label>
            <input type="number" name="experience" min="0" required
                value="<?= $coach->getExperience() ?>"
                class="w-full border rounded-lg p-3">
        </div>

        <div>
            <label class="font-semibold">Photo (URL)</label>
            <input type="text" name="photo"
                value="<?= htmlspecialchars($coach->getPhoto()) ?>"
                class="w-full border rounded-lg p-3">
        </div>

        <div>
            <label class="font-semibold">Certification</label>
            <input type="text" name="certification"
                value="<?= htmlspecialchars($coach->getCertification()) ?>"
                class="w-full border rounded-lg p-3">
        </div>

        <button class="bg-[#640D5F] text-white px-6 py-2 rounded-lg hover:opacity-90">
            Enregistrer
        </button>

    </form>

</div>
</body>
</html>
