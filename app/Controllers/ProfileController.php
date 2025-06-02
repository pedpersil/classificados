<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Ad;
use App\Models\User;

class ProfileController extends Controller
{
    private Ad $adModel;
    private User $userModel;

    public function __construct()
    {
        $this->adModel = new Ad();
        $this->userModel = new User();
    }

    public function index()
    {
        $this->checkAuth();
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $this->view('profile/index', ['userInfo' => $user]);
    }

    public function editForm()
    {
        $this->checkAuth();
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $this->view('profile/edit', ['userInfo' => $user]);
    }

    public function update()
    {
        $this->checkAuth();

        // Verifica se o e-mail já está cadastrado
        $existingUser = $this->userModel->findByEmail($_POST['email']);
        if ($existingUser['id'] !== $_SESSION[SESSION_NAME]['id']) {
            $_SESSION['error'] = 'Esse endereço de E-mail já está em uso.';
            header('Location: ' . BASE_URL . '/user/profile/edit');
            exit;
        }
    
        $userId = $_SESSION[SESSION_NAME]['id'];
    
        // Busca dados atuais do usuário
        $currentUser = $this->userModel->getById($userId);
    
        $data = [
            'name'       => $_POST['name'] ?? '',
            'email'      => $_POST['email'] ?? '',
            'phone'      => $_POST['phone'] ?? '',
            'address'    => $_POST['address'] ?? '',
            'city'       => $_POST['city'] ?? '',
            'state'      => $_POST['state'] ?? '',
            'zip_code'   => $_POST['zip_code'] ?? '',
            'rg'         => $_POST['rg'] ?? '',
            'cpf'        => $_POST['cpf'] ?? '',
            'gender'     => $_POST['gender'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? '',
        ];
    
        // Upload da nova foto
        if (!empty($_FILES['photo']['name'])) {
            $fileName = uniqid() . '_' . $_FILES['photo']['name'];
            $dest = __DIR__ . '/../../public/assets/profiles/' . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                // Remove a foto antiga, se não for a padrão
                if (!empty($currentUser['photo_url']) && $currentUser['photo_url'] !== 'assets/profiles/default.png') {
                    $oldPath = __DIR__ . '/../../public/' . $currentUser['photo_url'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
    
                // Salva nova foto no banco
                $data['photo_url'] = 'assets/profiles/' . $fileName;
            }
        } else {
            // Nenhuma nova imagem enviada → mantém a atual
            $data['photo_url'] = $currentUser['photo_url'];
        }
    
        $this->userModel->update($userId, $data);
        $_SESSION['success'] = 'Perfil atualizado com sucesso!';
        header('Location: ' . BASE_URL . '/user/profile');
        exit;
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

    public function fetch()
    {
        $this->checkAuth();
        
        $page = $_GET['page'] ?? 1;
        $recordsPerPage = $_GET['recordsPerPage'] ?? 15;

        $userId = $_SESSION[SESSION_NAME]['id'];

        // Calcula o offset para a consulta SQL
        $offset = ($page - 1) * $recordsPerPage;

        // Busca os anúncios com limite e offset
        $ads = $this->adModel->getAdsWithPaginationUserPanel($userId, $recordsPerPage, $offset);
        $totalAds = $this->adModel->getTotalAds();

        // Calcula o total de páginas
        $totalPages = ceil($totalAds / $recordsPerPage);

        // Gera o HTML dos anúncios
        $adsHtml = '';
        foreach ($ads as $ad) {
            $imageUrl = $this->adModel->getByIdWithImages($ad['id']);
            $adsHtml .= "
                <tr>
                    <td class='text-center align-middle'>
                        <img src='" . BASE_URL . "/" . (isset($imageUrl['images'][0]['image_path']) ? $imageUrl['images'][0]['image_path'] : 'assets/profiles/default.png') . "' alt='Imagem do anúncio' class='img-thumbnail' style='max-width: 80px; max-height: 80px;'>
                    </td>
                    <td>{$ad['title']}</td>
                    <td><span class='badge bg-" . ($ad['status'] == 'active' ? 'success' : 'secondary') . "'>" . ($ad['status'] == 'active' ? 'Ativo' : 'Inativo') . "</span></td>
                    <td>R$ " . number_format($ad['price'], 2, ',', '.') . "</td>
                    <td>
                        <a href='" . BASE_URL . "/user/profile/ads/edit/{$ad['id']}' class='btn btn-success btn-sm'>Editar</a>
                        <a href='" . BASE_URL . "/user/profile/ads/toggle/{$ad['id']}' class='btn btn-warning btn-sm'>Ativar/Inativar</a>
                        <a href='" . BASE_URL . "/user/profile/ads/delete/{$ad['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Deseja excluir este anúncio?\")'>Excluir</a>
                    </td>
                </tr>";
        }

        // Gera o HTML da paginação
        $paginationHtml = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $paginationHtml .= "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                                    <a class='page-link' href='#' data-page='$i'>$i</a>
                                </li>";
        }

        // Retorna a resposta JSON com os dados
        echo json_encode([
            'adsHtml' => $adsHtml,
            'paginationHtml' => $paginationHtml
        ]);
    }


}
