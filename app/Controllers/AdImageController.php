<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Ad;
use App\Models\User;

class AdImageController extends Controller
{
    private User $userModel;

    private Ad $adModel;
    
    public function __construct()
    {
        $this->adModel = new Ad();
        $this->userModel = new User();
    }

    public function delete($imageId)
    {
        $this->checkAuth();

        $image = $this->adModel->getImageById($imageId);

        if (!$image || $image['user_id'] != $_SESSION[SESSION_NAME]['id']) {
            $_SESSION['error'] = 'Acesso negado.';
            header('Location: ' . BASE_URL . '/user/profile/ads');
            exit;
        }

        if ($this->adModel->deleteImageById($imageId)) {
            if (file_exists(__DIR__ . '/../../public/' . $image['path'])) {
                unlink(__DIR__ . '/../../public/' . $image['path']);
            }
            $_SESSION['success'] = 'Imagem removida com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao remover imagem.';
        }

        header('Location: ' . BASE_URL . '/user/profile/ads/edit/' . $image['ad_id']);
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

}
