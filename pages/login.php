<?php
    session_start();

    require_once '../config/database.php';
    require_once '../classes/Utilisateur.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email   = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = "Tous les champs sont obligatoires.";
        } else {

            $pdo = Database::getConnection();
            $user = Utilisateur::login($pdo, $email, $password);
            if ($user) {
                // Création de la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom']     = $user['nom'];
                $_SESSION['prenom']  = $user['prenom'];
                $_SESSION['role']    = $user['role'];

                // Redirection selon rôle
                if ($user['role'] === 'coach') {
                    header('Location: ../pages/dashbord_coach.php');
                } else if ($user['role'] === 'sportif') {
                    header('Location: ../pages/dashbord_sportif.php');
                }else {
                    header('Location: ../public/index.php');
                }
                exit;
            }

        }
    }
?>



<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Connexion | CoachPro</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="min-h-screen grid grid-cols-1 md:grid-cols-2">

        <!-- LEFT : Form -->
        <div class="flex items-center justify-center px-8">
            <div class="w-full max-w-md">
                <h2 class="text-3xl font-bold text-center mb-6 text-purple-600">Connexion</h2>
                
                    <?php if (!empty($error)) : ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                <p class="text-gray-500 mb-6">Accédez à votre espace CoachPro</p>

                <form method="POST">
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Email</label>
                        <input type="email" name="email" class="w-full border rounded-lg px-4 py-2" required>
                    </div>

                    <div class="mb-6">
                        <label class="block mb-1 font-medium">Mot de passe</label>
                        <input type="password" name="password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>

                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-semibold">
                        Se connecter
                    </button>
                </form>

                <p class="text-sm text-gray-500 mt-4 text-center">
                    Pas encore de compte ?
                    <a href="register.php" class="text-purple-600 font-semibold">Inscription</a>
                </p>
            </div>
        </div>

        <!-- RIGHT : Image -->
        <div class="hidden md:block bg-cover bg-center" 
            style="background-image:url('../images/Sport.jpg');">
        </div>

</body>
</html>

