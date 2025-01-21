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

    public function createUser($numUtilisateur, $nom, $prenom, $age, $adresse, $login, $password, $numClub = null)
    {
        try {
            // Vérifier si l'utilisateur existe déjà
            $check = $this->conn->prepare("SELECT numUtilisateur FROM Utilisateur WHERE login = ?");
            $check->execute([$login]);
            if ($check->fetch()) {
                return false;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO Utilisateur (numUtilisateur, numClub, nom, prenom, age, adresse, login, mdp) 
                     VALUES (:numUtilisateur, :numClub, :nom, :prenom, :age, :adresse, :login, :mdp)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'numUtilisateur' => $numUtilisateur,
                'numClub' => $numClub,
                'nom' => $nom,
                'prenom' => $prenom,
                'age' => $age,
                'adresse' => $adresse,
                'login' => $login,
                'mdp' => $hashed_password
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function verifyLogin($p_Login, $password) 
    {
        try {
            // Modification de la requête pour récupérer tous les champs nécessaires
            $query = "SELECT numUtilisateur, nom, prenom, login, mdp 
                     FROM Utilisateur 
                     WHERE login = :login";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['login' => $p_Login]);
            $user = $stmt->fetch();
    
            if ($user && password_verify($password, $user['mdp'])) {
                return [
                    'success' => true,
                    'user' => [
                        'numUtilisateur' => $user['numUtilisateur'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'login' => $user['login']
                    ]
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Identifiants incorrects'
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du login : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }

    
    public function getConcours()
{
    try {
        $query = "SELECT numConcours, saison, annee, theme FROM Concours";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des concours : " . $e->getMessage());
        return [];
    }
}
}


