<?php
session_start();
require_once 'include/db.php';

class Auth {
    private $db;
    private $conn;
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const LOCKOUT_TIME = 900;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLogin();
        }

        if (!$this->validateCSRFToken()) {
            $this->setError("Session invalide, veuillez réessayer");
            $this->redirectToLogin();
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            $this->setError("Veuillez remplir tous les champs");
            $this->redirectToLogin();
        }

        if ($this->isAccountLocked($login)) {
            $this->setError("Compte temporairement bloqué. Veuillez réessayer plus tard");
            $this->redirectToLogin();
        }

        $user = $this->verifyCredentials($login, $password);
        
        if ($user) {
            $this->handleSuccessfulLogin($user);
        } else {
            $this->handleFailedLogin($login);
        }
    }

    private function verifyCredentials($login, $password) {
        try {
            $query = "SELECT * FROM Utilisateur WHERE login = :login";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['login' => $login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mdp'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du login : " . $e->getMessage());
            return false;
        }
    }

    private function validateCSRFToken(): bool {
        return isset($_POST['csrf_token']) && 
               isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    private function isAccountLocked(string $login): bool {
        $attempts = $_SESSION['login_attempts'][$login] ?? 0;
        $lastAttempt = $_SESSION['last_attempt'][$login] ?? 0;
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            if (time() - $lastAttempt < self::LOCKOUT_TIME) {
                return true;
            }
            $this->resetLoginAttempts($login);
        }
        return false;
    }

    private function handleSuccessfulLogin(array $user): void {
        session_regenerate_id(true);
        $this->resetLoginAttempts($user['login']);
        
        $_SESSION['user_id'] = $user['numUtilisateur'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['last_activity'] = time();
        
        $this->logLogin($user['numUtilisateur'], true);

        if ($user['type'] === 'admin') 
        {
            $this->redirectToadmin();
        }

        else if ($user['type'] === 'user') 
        {
            #TODO : Rediriger vers la page de l'utilisateur
        }

        else 
        {
            $this->redirectToHome();
        }
    }

    private function handleFailedLogin(string $login): void {
        $_SESSION['login_attempts'][$login] = ($_SESSION['login_attempts'][$login] ?? 0) + 1;
        $_SESSION['last_attempt'][$login] = time();
        
        $this->logLogin(null, false);
        $this->setError("Identifiants incorrects");
        $this->redirectToLogin();
    }

    private function resetLoginAttempts(string $login): void {
        unset($_SESSION['login_attempts'][$login]);
        unset($_SESSION['last_attempt'][$login]);
    }

    private function logLogin(?int $userId, bool $success): void {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        // Implémentation de la journalisation
    }

    private function generateCSRFToken(): string {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    private function redirectToadmin(): void {
        header('Location: admin/admin.php');
        exit;
    }

    private function redirectToHome(): void {
        header('Location: dashboard.php');
        exit;
    }

    private function redirectToLogin(): void {
        header('Location: index.php');
        exit;
    }

    private function setError(string $message): void {
        $_SESSION['error'] = $message;
    }
}

try {
    $auth = new Auth();
    $auth->authenticate();
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue, veuillez réessayer plus tard";
    header('Location: index.php');
    exit;
}
