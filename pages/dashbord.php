
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Sportif</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

<aside class="w-64 bg-[#C060A1] text-white flex flex-col p-6">

    <div class="p-6 text-xl font-bold">
        CoachPro
    </div>

    <nav class="mt-6 flex flex-col gap-2 px-4">

        <a href="#" class="px-4 py-2 hover:bg-[#C30E59] rounded">
             Dashboard
        </a>

      

        <a href="reservations.php" class="px-4 py-2 hover:bg-[#C30E59] rounded">
             Mes réservations
        </a>

        <a href="../auth/logout.php"
           class="mt-6 bg-[#DE1A58] py-2 text-center rounded">
            Déconnexion
        </a>

    </nav>
</aside>

<main class="flex-1 p-8 space-y-8">
    <h1 class="text-3xl font-bold mb-8 text-[#640D5F]">
        Dashboard Sportif
    </h1>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-xl p-6">
            <p class="text-gray-500">Séances réservées</p>
            <p class="text-4xl font-bold text-green-600"><?= $reserved ?></p>
        </div>

        <div class="bg-white shadow rounded-xl p-6">
            <p class="text-gray-500">Demandes en attente</p>
            <p class="text-4xl font-bold text-yellow-600"><?= $pending ?></p>
        </div>
        
    </div>
    <section class="mt-10">
    <h2 class="text-2xl font-bold text-[#640D5F] mb-6">
        Coachs disponibles
    </h2>

    <div class="grid md:grid-cols-3 gap-6">
    <?php while ($coach = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-xl shadow p-5">
            
            <img src="<?= htmlspecialchars ($coach['photo'] ?: '../images/profile.jpg')?>"
                 class="w-24 h-24 rounded-full mx-auto object-cover mb-4">

            <h3 class="text-center font-semibold text-lg">
                <?= htmlspecialchars($coach['prenom'].' '.$coach['nom']) ?>
            </h3>

           <p class="text-center text-gray-500 mb-2">
                <?= (int)$coach['experience'] ?> ans d'expérience
            </p>

            <p class="text-gray-600 text-sm mt-3 line-clamp-3">
                <?= htmlspecialchars($coach['biographie']) ?>
            </p>


            <a href="disponibilites.php?coach_id=<?= $coach['id'] ?>"
               class="block text-center mt-4 bg-[#640D5F] text-white py-2 rounded-lg hover:opacity-90">
                Voir disponibilités
            </a>
        </div>
    <?php endwhile; ?>
    </div>
</section>

</main>

</body>
</html>
