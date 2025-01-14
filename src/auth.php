<?php
session_start();
require_once 'include/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs";
        header('Location: index.php');
        exit;
    }

    $db = Database::getInstance();
    $user = $db->verifyLogin($login, $password);

    if ($user) {
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['role'] = $user['role'];

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        // Échec de la connexion
        $_SESSION['error'] = "Identifiants incorrects";
        header('Location: index.php');
        exit;
    }
} else {
    // Si quelqu'un accède directement à auth.php sans POST
    header('Location: index.php');
    exit;
}
