<?php
session_start();

require_once __DIR__ . '/../classes/Coach.php';

/* =====================
   AUTH CHECK
===================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit;
}

/* =====================
   LOAD COACH
===================== */
$coachId = (int) $_SESSION['user_id'];
$coach = new Coach($coachId);

$message = '';

/* =====================
   HANDLE FORM SUBMIT
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biographie    = trim($_POST['biographie']);
    $experience    = (int) $_POST['experience'];
   
    $photoPath = $coach->getPhoto();
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg','jpeg','png'])) {
            $photoPath = 'uploads/photos/' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../' . $photoPath);
        }
    }

    // Certification
    $certPath = $coach->getCertification();
    if (isset($_FILES['certification']) && $_FILES['certification']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['certification']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg','jpeg','png'])) {
            $certPath = 'uploads/certifications/' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['certification']['tmp_name'], __DIR__ . '/../' . $certPath);
        }
    }

    // Mettre à jour le profil
    $coach->profile($coachId, $biographie, $experience, $photoPath, $certPath);

    header('Location: profile.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-[#640D5F] mb-6">
        Mon Profil
    </h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data"class="bg-white p-6 rounded-xl shadow space-y-5">

        <div>
            <label class="block font-semibold mb-1">Biographie</label>
            <textarea name="biographie"
                      class="w-full border rounded-lg p-3"
                      rows="4"><?= htmlspecialchars($coach->getBiographie()) ?></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">Années d'expérience</label>
            <input type="number"
                   name="experience"
                   class="w-full border rounded-lg p-2"
                   value="<?= $coach->getExperience() ?>">
        </div>

        <div>
            <label class="block font-semibold mb-1">Photo (URL)</label>
            <input type="text"
                   name="photo"
                   class="w-full border rounded-lg p-2"
                   value="<?= htmlspecialchars($coach->getPhoto()) ?>">
        </div>

        <?php if ($coach->getPhoto()): ?>
            <img src="<?= htmlspecialchars($coach->getPhoto()) ?>"
                 class="w-32 h-32 object-cover rounded mt-2">
        <?php endif; ?>

      
<div>
        <label class="block text-gray-600 mb-1">Certification</label>
        <input type="file" name="certification" accept="image/png, image/jpeg" class="w-full border rounded-lg p-2"
                   value="<?= htmlspecialchars($coach->getCertification()) ?>">
    </div>
        <button
            class="bg-[#640D5F] text-white px-6 py-2 rounded-lg hover:opacity-90">
            Enregistrer
        </button>

    </form>

</div>

</body>
</html>
