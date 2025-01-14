<?php
require_once 'config.php';

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    // Fonction pour créer un nouvel utilisateur avec mot de passe hashé
    public function createUser($login, $password, $role = 'user')
    {
        try {
            // Vérifier si l'utilisateur existe déjà
            $check = $this->conn->prepare("SELECT id FROM utilisateurs WHERE login = ?");
            $check->execute([$login]);
            if ($check->fetch()) {
                return false; // Utilisateur existe déjà
            }

            // Hasher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insérer le nouvel utilisateur
            $query = "INSERT INTO utilisateurs (login, password, role) VALUES (:login, :password, :role)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'login' => $login,
                'password' => $hashed_password,
                'role' => $role
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    // Fonction pour vérifier les identifiants de connexion
    public function verifyLogin($login, $password)
    {
        try {
            $query = "SELECT * FROM utilisateurs WHERE login = :login";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['login' => $login]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du login : " . $e->getMessage());
            return false;
        }
    }
}
