<?php
namespace App\Controllers;

use Core\Controller;
use Config\Database;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        $this->view('auth/login');
    }

    public function login()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            $secret = '6LcGqz0rAAAAAF6HPq165hrq9z80tciBzsQAgEee'; // Chave secreta do Google

            $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $response = file_get_contents($verifyUrl . '?secret=' . $secret . '&response=' . $recaptchaResponse);
            $responseData = json_decode($response);

            if (!$responseData->success) {
                $_SESSION['login_error'] = 'Por favor, confirme que você não é um robô.';
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = 'Preencha todos os campos.';
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            $pdo = Database::connect();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user['status'] === 'inactive') {
                $_SESSION['login_error'] = 'Usuário desativado.';
                header('Location: ' . BASE_URL . '/login');
                exit;
            }           

            if (!$user['email_verified']) {
                $_SESSION['login_error'] = "Você precisa confirmar seu e-mail antes de fazer login.";
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            if ($user && password_verify($password, $user['password'])) {
                // Gera um novo hash único
                $verificationHash = bin2hex(random_bytes(32));

                // Atualiza no banco de dados
                $update = $pdo->prepare("UPDATE users SET verification_hash = :hash WHERE id = :id");
                $update->execute([
                    ':hash' => $verificationHash,
                    ':id'   => $user['id']
                ]);

                // Define sessão
                $_SESSION[SESSION_NAME] = [
                    'id'                => $user['id'],
                    'name'              => $user['name'],
                    'email'             => $user['email'],
                    'role'              => $user['role'],
                    'verification_hash' => $verificationHash
                ];

                // Redireciona conforme o tipo de usuário
                $redirectUrl = ($user['role'] === 'admin') ? BASE_URL . '/admin' : BASE_URL . '/';
                header('Location: ' . $redirectUrl);
                exit;
            }

            $_SESSION['login_error'] = 'Email ou senha inválidos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }    

    public function logout()
    {
        unset($_SESSION[SESSION_NAME]);
        session_destroy();
        header('Location: ' . BASE_URL . '/');
    }

    public function verifyEmail()
    {
        $pdo = Database::connect();
        
        $token = $_GET['token'] ?? null;

        if (!$token) {
            $_SESSION['login_error'] = "Token inválido.";
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email_token = :token AND email_verified = 0");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            $update = $pdo->prepare("UPDATE users SET email_verified = 1, email_token = NULL WHERE id = :id");
            $update->execute([':id' => $user['id']]);

            $_SESSION['success'] = "E-mail confirmado com sucesso. Faça login.";
            header('Location: ' . BASE_URL . '/login');
            exit;
        } else {
            $_SESSION['login_error'] = "Token inválido ou e-mail já confirmado.";
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function forgotPasswordForm()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        $this->view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        
        $email = $_POST['email'] ?? null;

        if (!$email) {
            $_SESSION['flash_error'] = 'E-mail é obrigatório.';
            header('Location: ' . BASE_URL . '/forgot_password');
            exit;
        }

        $userModel = new \App\Models\User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            // Não revela se o e-mail existe
            $_SESSION['flash_success'] = 'Se o e-mail existir, você receberá um link de redefinição.';
            header('Location: ' . BASE_URL . '/forgot_password');
            exit;
        }

        // Gerar token e expiração
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $userModel->setResetToken($user['id'], $token, $expiration);

        // Criar link de redefinição
        $resetLink = BASE_URL . "/reset_password?token={$token}";
        $userName = htmlspecialchars($user['name']);

        // E-mail formatado
        $subject = "Redefinição de Senha - Classificados Taperoá";

        $message = "
            <div style=\"background-color:#f7f7f7;padding:20px;font-family:Arial,sans-serif;\">
                <div style=\"max-width:600px;margin:auto;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,0.1);\">
                    <div style=\"background-color:#4a90e2;padding:20px;text-align:center;color:white;\">
                        <h2 style=\"margin:0;\">Classificados Taperoá</h2>
                    </div>
                    <div style=\"padding:30px;text-align:left;color:#333;\">
                        <p>Olá, <strong>{$userName}</strong>!</p>
                        <p>Recebemos uma solicitação para redefinir sua senha no nosso sistema de classificados.</p>
                        <p>Para criar uma nova senha, clique no botão abaixo:</p>
                        <p style=\"text-align:center;\">
                            <a href='{$resetLink}'
                            style=\"display:inline-block;padding:12px 20px;background-color:#4a90e2;color:white;text-decoration:none;border-radius:5px;font-weight:bold;\">
                                Redefinir Senha
                            </a>
                        </p>
                        <p>Esse link é válido por 1 hora. Caso você não tenha solicitado a redefinição, ignore este e-mail.</p>
                        <hr style=\"margin:30px 0;border:none;border-top:1px solid #eee;\">
                        <p style=\"font-size:12px;color:#888;text-align:center;\">
                            Este e-mail foi enviado por <strong>Classificados Taperoá</strong><br>
                            Desenvolvido por <a href='https://pedrosilva.tech' style='color:#4a90e2;text-decoration:none;'>pedrosilva.tech</a>
                        </p>
                    </div>
                </div>
            </div>
        ";

        // Enviar o e-mail
        $mailer = new \App\Helpers\Mailer();
        $mailer->send($user['email'], $subject, $message);

        $_SESSION['flash_success'] = 'Se o e-mail existir, você receberá um link de redefinição.';
        header('Location: ' . BASE_URL . '/forgot_password');
        exit;
    }

    public function showResetForm()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        $this->view('auth/reset_password');
    }

    public function saveNewPassword()
    {
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$token || !$password || !$confirmPassword) {
            $_SESSION['flash_error'] = 'Preencha todos os campos.';
            header('Location: ' . BASE_URL . '/reset_password?token=' . urlencode($token));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'A senha deve ter pelo menos 6 caracteres.';
            header('Location: ' . BASE_URL . '/reset_password?token=' . urlencode($token));
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['flash_error'] = 'As senhas não coincidem.';
            header('Location: ' . BASE_URL . '/reset_password?token=' . urlencode($token));
            exit;
        }

        $userModel = new \App\Models\User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['flash_error'] = 'Token inválido.';
            header('Location: ' . BASE_URL . '/reset_password');
            exit;
        }

        // Verifica se o token expirou
        if (strtotime($user['reset_expiration']) < time()) {
            $_SESSION['flash_error'] = 'Token expirado. Solicite uma nova redefinição.';
            header('Location: ' . BASE_URL . '/forgot_password');
            exit;
        }

        // Atualiza a senha
        $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        $userModel->updatePassword($user['id'], $newPasswordHash);

        // Limpa o token
        $userModel->clearResetToken($user['id']);

        $_SESSION['success'] = 'Senha redefinida com sucesso. Faça login com sua nova senha.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }


}
