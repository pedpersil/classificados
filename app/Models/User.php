<?php
namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Config\Database;
use PDO;

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT id, name, email, role, status, verification_hash, phone, address, state, zip_code, rg, cpf, gender, birth_date, photo_url, city, user_type, created_at, updated_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO users 
            (name, email, password, role, phone, address, city, state, zip_code, rg, cpf, gender, birth_date, photo_url, created_at) 
            VALUES 
            (:name, :email, :password, :role, :phone, :address, :city, :state, :zip_code, :rg, :cpf, :gender, :birth_date, :photo_url, NOW())";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }


    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                id, name, email, password, role, phone, address, city, state, zip_code, rg, cpf, gender, birth_date, photo_url, status, created_at, updated_at 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function update($id, $data)
    {
        $columns = [];
        foreach ($data as $key => $value) {
            $columns[] = "$key = :$key";
        }
        $sql = "UPDATE users SET " . implode(', ', $columns) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setResetToken($userId, $token, $expiration)
    {
        $sql = "UPDATE users SET reset_token = :token, reset_expiration = :expiration WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':expiration' => $expiration,
            ':id' => $userId
        ]);
    }


    public function delete($id)
    {
        // 1. Excluir as imagens dos anúncios
        // Buscar todos os caminhos das imagens associadas aos anúncios do usuário
        $stmt = $this->pdo->prepare("SELECT image_path FROM ad_images 
                                    WHERE ad_id IN (SELECT id FROM ads WHERE user_id = :user_id)");
        $stmt->execute([':user_id' => $id]);
        $adImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Excluir as imagens do diretório
        foreach ($adImages as $image) {
            // Corrigir o caminho completo da imagem, utilizando o caminho correto para o diretório de uploads
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . BASE_PATH .'/assets/uploads/' . basename($image['image_path']);
            
            // Verificar se o arquivo existe e tentar excluí-lo
            if (file_exists($imagePath)) {
                if (!unlink($imagePath)) {
                    // Caso não consiga excluir a imagem, registrar o erro
                    error_log("Erro ao excluir a imagem: " . $imagePath);
                }
            } else {
                // Caso o arquivo não exista, registrar o erro
                error_log("Imagem não encontrada: " . $imagePath);
            }
        }

        // 2. Excluir os registros da tabela ad_images (imagens dos anúncios)
        $stmt = $this->pdo->prepare("DELETE FROM ad_images WHERE ad_id IN (SELECT id FROM ads WHERE user_id = :user_id)");
        $stmt->execute([':user_id' => $id]);

        // 3. Excluir os anúncios da tabela ads
        $stmt = $this->pdo->prepare("DELETE FROM ads WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $id]);

        // 4. Excluir a foto de perfil do usuário, se existir
        $stmt = $this->pdo->prepare("SELECT photo_url FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['photo_url'])) {
            // Corrigir o caminho da foto de perfil
            $profileImagePath = $_SERVER['DOCUMENT_ROOT'] . BASE_PATH .'/assets/profiles/' . basename($user['photo_url']);
            
            // Verificar se o arquivo existe e tentar excluí-lo
            if (file_exists($profileImagePath)) {
                if (!unlink($profileImagePath)) {
                    // Caso não consiga excluir a imagem, registrar o erro
                    error_log("Erro ao excluir a foto de perfil: " . $profileImagePath);
                }
            } else {
                // Caso o arquivo não exista, registrar o erro
                error_log("Foto de perfil não encontrada: " . $profileImagePath);
            }
        }

        // 5. Excluir o usuário da tabela 'users'
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function protectedRoute(String $role = 'User'): bool
    {
        if (!isset($_SESSION[SESSION_NAME])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT verification_hash FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $_SESSION[SESSION_NAME]['id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        if (!$user || !hash_equals($user['verification_hash'], $_SESSION[SESSION_NAME]['verification_hash'])) {
            unset($_SESSION[SESSION_NAME]);
            session_destroy();
            return false;
        } else {
            return true;
        }
       
    }

    public function sendConfirmationEmail($toEmail, $userName, $token): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'classificados@pedrosilva.tech'; // substitua
            $mail->Password = '9M#l9a4ciF=U'; // substitua
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('classificados@pedrosilva.tech', 'Classificados Taperoá');
            $mail->addAddress($toEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = 'Confirme seu e-mail em Classificados Taperoá';
            $mail->Body = "
                <div style=\"background-color:#f7f7f7;padding:20px;font-family:Arial,sans-serif;\">
                    <div style=\"max-width:600px;margin:auto;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,0.1);\">
                        <div style=\"background-color:#4a90e2;padding:20px;text-align:center;color:white;\">
                            <h2 style=\"margin:0;\">Classificados Taperoá</h2>
                        </div>
                        <div style=\"padding:30px;text-align:left;color:#333;\">
                            <p>Olá, <strong>{$userName}</strong>!</p>
                            <p>Obrigado por se cadastrar em nosso sistema de classificados.</p>
                            <p>Para ativar sua conta e começar a anunciar, confirme seu e-mail clicando no botão abaixo:</p>
                            <p style=\"text-align:center;\">
                                <a href='" . BASE_URL . "/verify_email?token={$token}' 
                                style=\"display:inline-block;padding:12px 20px;background-color:#4a90e2;color:white;text-decoration:none;border-radius:5px;font-weight:bold;\">
                                    Confirmar E-mail
                                </a>
                            </p>
                            <p>Se você não realizou esse cadastro, apenas ignore este e-mail.</p>
                            <hr style=\"margin:30px 0;border:none;border-top:1px solid #eee;\">
                            <p style=\"font-size:12px;color:#888;text-align:center;\">
                                Este e-mail foi enviado por <strong>Classificados Taperoá</strong><br>
                                Desenvolvido por <a href='https://pedrosilva.tech' style='color:#4a90e2;text-decoration:none;'>pedrosilva.tech</a>
                            </p>
                        </div>
                    </div>
                </div>
            ";



            $mail->send();

            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar confirmação: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function findByResetToken($token)
    {
        $sql = "SELECT * FROM users WHERE reset_token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function clearResetToken($userId)
    {
        $sql = "UPDATE users SET reset_token = NULL, reset_expiration = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }

}
