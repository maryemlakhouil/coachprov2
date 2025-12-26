<?php
session_start();


// Si l'utilisateur est connecté
if (isset($_SESSION['user_id'], $_SESSION['role'])) {

    // Redirection selon le rôle
    if ($_SESSION['role'] === 'sportif') {
        header('Location: ../pages/dashbord_sportif.php');
        exit;
    }

    if ($_SESSION['role'] === 'coach') {
        header('Location: ../pages/dashbord_coach.php');
        exit;
    }

    // Rôle inconnu  sécurité
    session_destroy();
    header('Location: ../pages/login.php');
    exit;
}

// Non connecté  login
header('Location: ../pages/login.php');
exit;
?>