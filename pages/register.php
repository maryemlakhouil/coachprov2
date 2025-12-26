<?php
require_once '../config/database.php';
require_once '../classes/Utilisateur.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($nom) ||empty($prenom) ||empty($email) ||empty($password) ||empty($role)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        $user = new Utilisateur();

        $result = $user->register($nom,$prenom,$email,$password,$role);

        if ($result) {
            $success = "Inscription réussie. Vous pouvez vous connecter.";
        } else {
            $error = "Email déjà utilisé ou rôle invalide.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Inscription | CoachPro</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="min-h-screen grid grid-cols-1 md:grid-cols-2">

        <!-- LEFT : Image -->
        <div class="hidden md:block bg-cover bg-center"
            style="background-image:url('../images/Sport1.jpg');">
        </div>

        <!-- RIGHT : Form -->
        <div class="flex items-center justify-center px-8">
            <div class="w-full max-w-md">
                <h1 class="text-3xl font-bold text-center mb-6 text-purple-600">Créer un compte</h1>
                <?php if ($error) : ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success) : ?>
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <p class="text-gray-500 mb-6">Rejoignez la communauté CoachPro</p>

                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="nom" placeholder="Nom" class="border p-3 rounded-lg">
                        <input type="text" name="prenom" placeholder="Prénom" class="border p-3 rounded-lg">
                    </div>

                    <input type="email" name="email" placeholder="Email" class="w-full border p-3 rounded-lg">

                    <input type="password" name ="password" placeholder="Mot de passe" class="w-full border p-3 rounded-lg">

                    <select name="role" class="w-full border p-3 rounded-lg">
                        <option value="">Choisir un rôle</option>
                        <option value="sportif">Sportif</option>
                        <option value="coach">Coach</option>
                    </select>

                    <button
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold">
                        S'inscrire
                    </button>
                </form>

                <p class="text-sm text-gray-500 mt-4 text-center">
                    Déjà inscrit ?
                    <a href="login.php" class="text-purple-600 font-semibold">Connexion</a>
                </p>
            </div>
        </div>

</body>
</html>
