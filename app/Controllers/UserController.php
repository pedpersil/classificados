<?php
namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Config\Database;
use PDO;

class UserController extends Controller
{
    private PDO $pdo;
    private User $userModel;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->userModel = new User();
    }

    public function index()
    {
        $this->checkAdmin();

        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $users = $this->userModel->getAll();
        $this->view('users/index', ['users' => $users, 'userInfo' => $user]);
    }

    public function createForm()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $this->view('users/create');
    }

    public function store()
    {
        if (!empty($_SESSION[SESSION_NAME]['email'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        
        // Verifica se as senhas coincidem
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = 'As senhas não coincidem.';
            header('Location: ' . BASE_URL . '/user/create');
            exit;
        }

        // Verifica se o e-mail já está cadastrado
        $existingUser = $this->userModel->findByEmail($_POST['email']);
        if ($existingUser) {
            $_SESSION['error'] = 'Esse endereço de E-mail já está em uso.';
            header('Location: ' . BASE_URL . '/user/create');
            exit;
        }

        // Processa o upload da foto
        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            $fileName = uniqid() . '_' . $_FILES['photo']['name'];
            $destination = __DIR__ . '/../../public/assets/profiles/' . $fileName;
            move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
            $photoPath = 'assets/profiles/' . $fileName;
        }

        // Dados do usuário
        $data = [
            'name'        => $_POST['name'] ?? '',
            'email'       => $_POST['email'] ?? '',
            'password'    => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'        => 'user',
            'phone'       => $_POST['phone'] ?? '',
            'address'     => $_POST['address'] ?? '',
            'city'        => $_POST['city'] ?? '',
            'state'       => $_POST['state'] ?? '',
            'zip_code'    => $_POST['zip_code'] ?? '',
            'rg'          => $_POST['rg'] ?? '',
            'cpf'         => $_POST['cpf'] ?? '',
            'gender'      => $_POST['gender'] ?? '',
            'birth_date'  => $_POST['birth_date'] ?? '',
            'photo_url'   => $photoPath
        ];

        // Cria o usuário no banco de dados
        if ($this->userModel->create($data)) {

            $token = bin2hex(random_bytes(32));

            $userInfo = $this->userModel->findByEmail($_POST['email']);

            $stmt = $this->pdo->prepare("UPDATE users SET email_token = :token WHERE id = :id");
            $stmt->execute([':token' => $token, ':id' => $userInfo['id']]);

            if ($this->userModel->sendConfirmationEmail($userInfo['email'], $userInfo['name'], $token)) {
                $_SESSION['success'] = "E-mail enviado. Verifique seu inbox para confirmar sua conta.";
                header('Location: ' . BASE_URL . '/login');
                exit;
            } else {
                $_SESSION['login_error'] = "Erro ao enviar o e-mail de confirmação.";
                header('Location: ' . BASE_URL . '/login');
                exit;    
            }

        }
        
        $_SESSION['login_error'] = "Erro ao criar novo usuário.";
        header('Location: ' . BASE_URL . '/login');
        exit;

    }


    // Exibir o formulário para alterar a senha
    public function showChangePasswordForm()
    {
        $role = $_SESSION[SESSION_NAME]['role'];
        if ($role == "admin") {
            $this->checkAdmin();
        } else {
            $this->checkAuth();
        }

        $this->view('users/change_password');
    }

    // Processar a alteração de senha
    public function changePassword()
    {
        $this->checkAuth();
        
        $userId = $_SESSION[SESSION_NAME]['id'];
        $user = $this->userModel->getById($userId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Verificar se a senha atual está correta
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['password_error'] = 'A senha atual está incorreta.';
                header('Location: ' . BASE_URL . '/user/password');
                exit;
            }

            // Validar nova senha
            if ($newPassword !== $confirmPassword) {
                $_SESSION['password_error'] = 'As senhas não coincidem.';
                header('Location: ' . BASE_URL . '/user/password');
                exit;
            }

            // Atualizar a senha
            $this->userModel->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
            
            $_SESSION['success'] = 'Senha alterada com sucesso!';
            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }
    }

    // Exibir o formulário de exclusão de conta
    public function showDeleteAccountForm()
    {
        $this->checkAuth();

        $userId = $_SESSION[SESSION_NAME]['id'];
        $user = $this->userModel->getById($userId);

        $this->view('users/delete_account', ['userInfo' => $user]);
    }

    // Processar a exclusão de conta
    public function deleteAccount()
    {
        $this->checkAuth();
        
        $userId = $_SESSION[SESSION_NAME]['id'];
        $this->userModel->delete($userId);

        // Logout após a exclusão da conta
        unset($_SESSION[SESSION_NAME]);
        session_destroy();

        $_SESSION['success'] = 'Sua conta foi excluída com sucesso!';
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    private function isAdmin()
    {
        $protected = $this->userModel->protectedRoute('Admin');
        return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['role'] === 'admin' && $protected;
    }

    private function checkAdmin()
    {
        if (!$this->isAdmin()) {
            header('Location:'. BASE_URL .'/');
            exit;
        }
    }

    private function isUser()
    {
        $protected = $this->userModel->protectedRoute('User');
        return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['role'] === 'user' && $protected;
    }
    
    private function checkAuth()
    {
        if (!$this->isUser()) {
            header('Location:'. BASE_URL .'/');
            exit;
        }
    } 

    public function changePasswordForm()
    {
        $role = $_SESSION[SESSION_NAME]['role'];
        if ($role == "admin") {
            $this->checkAdmin();
        } else {
            $this->checkAuth();
        }

        $userId = $_SESSION[SESSION_NAME]['id'];
        $user = $this->userModel->getById($userId);
        $this->view('users/change_password', ['userInfo' => $user]);
    }

    public function updatePassword()
    {
        $role = $_SESSION[SESSION_NAME]['role'];
        if ($role == "admin") {
            $this->checkAdmin();
        } else {
            $this->checkAuth();
        }

        $userId = $_SESSION[SESSION_NAME]['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Verificações básicas
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            if ($role == "admin") {
                header('Location: ' . BASE_URL . '/admin/profile/password');
                exit;                
            } else {
                header('Location: ' . BASE_URL . '/user/profile/password');
                exit;
            }
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "A nova senha e a confirmação não coincidem.";
            if ($role == "admin") {
                header('Location: ' . BASE_URL . '/admin/profile/password');
                exit;                
            } else {
                header('Location: ' . BASE_URL . '/user/profile/password');
                exit;
            }
        }

        // Busca usuário
        $user = $this->userModel->getById($userId);
        if (!$user['id'] || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = "Senha atual incorreta.";
            if ($role == "admin") {
                header('Location: ' . BASE_URL . '/admin/profile/password');
                exit;                
            } else {
                header('Location: ' . BASE_URL . '/user/profile/password');
                exit;
            }
        }

        // Atualiza senha
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->updatePassword($userId, $hashed);

        $_SESSION['success'] = "Senha alterada com sucesso!";
        if ($role == "admin") {
            header('Location: ' . BASE_URL . '/admin/profile/password');
            exit;                
        } else {
            header('Location: ' . BASE_URL . '/user/profile/password');
            exit;
        }
    }

    public function toggleStatus($id)
    {
        $this->checkAdmin();

        $user = $this->userModel->getById($id);
        if (!$user || $user['role'] !== 'user') {
            $_SESSION['error'] = 'Usuário inválido.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->update($id, ['status' => $newStatus]);

        // Desativa anúncios se necessário
        if ($newStatus === 'inactive') {
            $db = (new Database())->connect();
            $stmt = $db->prepare("UPDATE ads SET status = 'inactive' WHERE user_id = ?");
            $stmt->execute([$id]);
        }

        $_SESSION['success'] = "Status de usuário atualizado com sucesso.";
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function delete($id)
    {
        $this->checkAdmin();

        $user = $this->userModel->getById($id);
        if (!$user || $user['role'] !== 'user') {
            $_SESSION['error'] = 'Usuário inválido.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Deletar imagem do perfil
        if ($user['photo_url'] && file_exists(__DIR__ . '/../../public/' . $user['photo_url'])) {
            unlink(__DIR__ . '/../../public/' . $user['photo_url']);
        }

        // Buscar e deletar imagens dos anúncios
        $db = (new Database())->connect();
        $stmt = $db->prepare("SELECT id FROM ads WHERE user_id = ?");
        $stmt->execute([$id]);
        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ads as $ad) {
            $adId = $ad['id'];

            // Buscar imagens
            $imgStmt = $db->prepare("SELECT image_path FROM ad_images WHERE ad_id = ?");
            $imgStmt->execute([$adId]);
            $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($images as $img) {
                $imgPath = __DIR__ . '/../../public/' . $img['image_path'];
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }

            // Excluir imagens do banco
            $db->prepare("DELETE FROM ad_images WHERE ad_id = ?")->execute([$adId]);
        }

        // Excluir os anúncios
        $db->prepare("DELETE FROM ads WHERE user_id = ?")->execute([$id]);

        // Excluir usuário
        $this->userModel->delete($id);

        $_SESSION['success'] = 'Usuário e dados associados removidos com sucesso.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function fetch()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $recordsPerPage = isset($_GET['recordsPerPage']) ? (int)$_GET['recordsPerPage'] : 15;
        $offset = ($page - 1) * $recordsPerPage;

        // Consultar os usuários com paginação
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT :offset, :recordsPerPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Contar o total de usuários
        $totalStmt = $this->pdo->prepare("SELECT COUNT(*) FROM users");
        $totalStmt->execute();
        $totalUsers = $totalStmt->fetchColumn();
        $totalPages = ceil($totalUsers / $recordsPerPage);

        // Gerar o HTML da tabela de usuários
        $usersHtml = '';
        foreach ($users as $user) {
            // Verifica se o usuário é admin e decide se as ações devem ser mostradas
            $actionsHtml = '';
            if ($user['role'] !== 'admin') {
                $actionsHtml = "<a href='" . BASE_URL . "/admin/users/toggle/{$user['id']}' class='btn btn-sm btn-outline-" . ($user['status'] === 'active' ? 'secondary' : 'success') . "'>
                                    " . ($user['status'] === 'active' ? 'Desativar' : 'Ativar') . "
                                </a>
                                <a href='" . BASE_URL . "/admin/users/delete/{$user['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir este usuário e todos os seus dados?\");'>
                                    Deletar
                                </a>";
            } else {
                $actionsHtml = "Nenhuma ação disponível.";
            }

            $usersHtml .= "<tr>
                <td>{$user['id']}</td>
                <td>{$user['name']}</td>
                <td>{$user['email']}</td>
                <td>" . ($user['role'] == 'admin' ? 'Administrador' : 'Usuário') . "</td>
                <td>
                    <span class='badge bg-" . ($user['status'] === 'active' ? 'success' : 'secondary') . "'>
                        " . ($user['status'] == 'active' ? 'Ativo' : 'Inativo') . "
                    </span>
                </td>
                <td>" . date('d/m/Y H:i', strtotime($user['created_at'])) . "</td>
                <td>
                    {$actionsHtml}
                </td>
            </tr>";
        }

        // Gerar a navegação da paginação (max 5 páginas por vez)
        $paginationHtml = '';
        $maxPagesToShow = 5;
        $startPage = max(1, $page - 2); // Página inicial
        $endPage = min($totalPages, $page + 2); // Página final

        if ($page > 1) {
            $paginationHtml .= "<li class='page-item'>
                <a class='page-link' href='#' data-page='1'>&lt;&lt;</a>
            </li>";
        }

        // Exibe as páginas de 1 a 5 conforme o cálculo de intervalo
        for ($i = $startPage; $i <= $endPage; $i++) {
            $paginationHtml .= "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                <a class='page-link' href='#' data-page='{$i}'>{$i}</a>
            </li>";
        }

        if ($page < $totalPages) {
            $paginationHtml .= "<li class='page-item'>
                <a class='page-link' href='#' data-page='{$totalPages}'>&gt;&gt;</a>
            </li>";
        }

        // Retornar os dados em formato JSON
        echo json_encode([
            'usersHtml' => $usersHtml,
            'paginationHtml' => $paginationHtml
        ]);
    }


}
