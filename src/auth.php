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
        if (!isset($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
            error_log("CSRF tokens manquants");
            return false;
        }

        if (!isset($_SESSION['csrf_token_time'])) {
            error_log("Temps CSRF manquant");
            return false;
        }

        if ((time() - $_SESSION['csrf_token_time']) > 3600) {
            error_log("Token CSRF expiré");
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }

        $valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
        if (!$valid) {
            error_log("Tokens CSRF ne correspondent pas");
        }
        return $valid;
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

    private function getUserRole(int $userId): string {
        $query = "SELECT 
                CASE 
                WHEN a.numAdmin IS NOT NULL THEN 'admin'
                WHEN c.numCompetiteur IS NOT NULL THEN 'competiteur'
                WHEN e.numEvaluateur IS NOT NULL THEN 'evaluateur'
                ELSE 'user'
            END AS role
        FROM Utilisateur u
        LEFT JOIN Admin a ON u.numUtilisateur = a.numAdmin
        LEFT JOIN Competiteur c ON u.numUtilisateur = c.numCompetiteur
        LEFT JOIN Evaluateur e ON u.numUtilisateur = e.numEvaluateur
        WHERE u.numUtilisateur = :userId";
    
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['userId' => $userId]);
            $role = $stmt->fetchColumn();
            $this->debugLog("Rôle récupéré de la base", ['user_id' => $userId, 'role' => $role]);
            return $role ?: 'user';
        } catch (PDOException $e) {
            $this->debugLog("Erreur lors de la récupération du rôle", ['error' => $e->getMessage()]);
            return 'user';
        }
    }

    private function handleSuccessfulLogin(array $user): void {
        $this->debugLog("Début handleSuccessfulLogin", ['user_id' => $user['numUtilisateur']]);
        
        session_regenerate_id(true);
        $this->resetLoginAttempts($user['login']);
        
        $_SESSION['user_id'] = $user['numUtilisateur'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['last_activity'] = time();
        
        $userRole = $this->getUserRole($user['numUtilisateur']);
        $_SESSION['role'] = $userRole;
        
        $this->debugLog("Rôle utilisateur récupéré", ['role' => $userRole]);
        
        $this->generateCSRFToken();
        $this->logLogin($user['numUtilisateur'], true);
        
        $this->debugLog("Redirection en cours", ['role' => $userRole]);
        
        if ($userRole === 'admin') {
            $this->debugLog("Redirection vers admin");
            header('Location: pages/admin/admin.php');
            exit;
        } elseif ($userRole === 'competiteur') {
            header('Location: pages/competiteur/dashboard.php');
            exit;
        } elseif ($userRole === 'evaluateur') {
            header('Location: pages/evaluateur/dashboard.php');
            exit;
        } else {
            header('Location: dashboard.php');
            exit;
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
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    private function redirectToLogin(): void {
        header('Location: index.php');
        exit;
    }

    private function setError(string $message): void {
        $_SESSION['error'] = $message;
    }

    private function debugLog($message, $data = null): void {
        $logMessage = date('Y-m-d H:i:s') . " - " . $message;
        if ($data !== null) {
            $logMessage .= " - Data: " . print_r($data, true);
        }
        error_log($logMessage);
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
