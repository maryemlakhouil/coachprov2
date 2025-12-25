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
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['role']    = $user['role'];

                header('Location:../pages/dashbord_coach.php');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Connexion</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gray-100 flex items-center justify-center min-h-screen">

        <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md">

            <h1 class="text-2xl font-bold text-center text-purple-600 mb-6">
                Connexion
            </h1>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">

                <div>
                    <label class="block text-gray-600 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full border rounded-lg px-4 py-2"
                        required>
                </div>

                <div>
                    <label class="block text-gray-600 mb-1">Mot de passe</label>
                    <input type="password" name="password"
                        class="w-full border rounded-lg px-4 py-2"
                        required>
                </div>

                <button type="submit"
                        class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-gray-600 mt-4">
                Pas encore de compte ?
                <a href="register.php" class="text-purple-600 font-semibold">
                    Inscription
                </a>
            </p>

        </div>

</body>
</html>

