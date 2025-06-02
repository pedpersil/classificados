<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\AdRating;

class AdController extends Controller
{
    private Ad $adModel;
    private User $userModel;
    private Category $categoryModel;
    private AdRating $adRatingModel;

    public function __construct()
    {
        $this->adModel = new Ad();
        $this->userModel = new User();
        $this->categoryModel = new Category();
        $this->adRatingModel = new AdRating();
    }

    public function index()
    {
        $this->checkAuth();
        $userId = $_SESSION[SESSION_NAME]['id'];
        $ads = $this->adModel->getByUser($userId);
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $this->view('ads/index', ['ads' => $ads, 'userInfo' => $user]);
    }

    public function createForm()
    {
        $this->checkAuth();
        $userId = $_SESSION[SESSION_NAME]['id'];
        $user = $this->userModel->getById($userId);
        $categories = $this->categoryModel->all();
        $this->view('ads/create', ['userInfo' => $user, 'categories' => $categories]);
    }

    public function store()
    {
        $this->checkAuth();

        $userId = $_SESSION[SESSION_NAME]['id'];
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $categoryId = $_POST['category_id'] ?? '';

        $adId = $this->adModel->create($userId, $categoryId, $title, $description, $price);

        // Upload das imagens
        if (!empty($_FILES['images']['name'][0])) {
            $this->adModel->uploadImages($adId, $_FILES['images']);
        }

        $_SESSION['success'] = 'Anúncio criado com sucesso!';
        header('Location: '. BASE_URL .'/user/profile/ads');
    }

    public function toggleStatus($id)
    {
        $this->checkAuth();
        $this->adModel->toggleStatus($id, $_SESSION[SESSION_NAME]['id']);
        $_SESSION['success'] = 'Status do anúncio atualizado com sucesso!';
        header('Location: '. BASE_URL .'/user/profile/ads');
    }

    public function delete($id)
    {
        $this->checkAuth();
        $this->adModel->delete($id, $_SESSION[SESSION_NAME]['id']);
        header('Location: '. BASE_URL .'/user/profile/ads');
    }

    private function isUser()
    {
        return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['role'] === 'user';
    }
    
    private function checkAuth()
    {
        if (!$this->isUser()) {
            header('Location:'. BASE_URL .'/');
            exit;
        }
    }

    public function show($id)
    {
        $ad = $this->adModel->getById($id);

        $average = $this->adRatingModel->getAverageRating($id);

        $userRating = null;

        if (!empty($_SESSION[SESSION_NAME]['id'])) {
            $userRating = $this->adRatingModel->getUserRating($_SESSION[SESSION_NAME]['id'], $ad['id']);
        }

        if (!isset($ad['id'])) {
            http_response_code(404);
            echo "Anúncio não encontrado.";
            return;
        }

        // REGISTRAR ID na sessão
        if (!isset($_SESSION['recent_ads'])) {
            $_SESSION['recent_ads'] = [];
        }

        // Evita duplicidade
        if (!in_array($id, $_SESSION['recent_ads'])) {
            array_unshift($_SESSION['recent_ads'], $id);
            // Limita a 10 anúncios
            $_SESSION['recent_ads'] = array_slice($_SESSION['recent_ads'], 0, 10);
        }

        if (isset($_SESSION[SESSION_NAME]) && $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id'])) {
            $this->view('ads/show', ['ad' => $ad, 'userInfo' => $user, 'average' => $average, 'userRating' => $userRating]);
        } else {
            $this->view('ads/show', ['ad' => $ad, 'average' => $average, 'userRating' => $userRating]);
        }
    }

    public function editForm($id)
    {
        $this->checkAuth();

        $userId = $_SESSION[SESSION_NAME]['id'];
        //$ad = $this->adModel->getByIdWithImages($id); // <-- aqui está a mudança

        $ad = $this->adModel->getById($id);

        if (!$ad || $ad['user_id'] != $userId) {
            http_response_code(404);
            echo "Anúncio não encontrado ou acesso não autorizado.";
            exit;
        }

        $categories = $this->categoryModel->all();
        $user = $this->userModel->getById($userId);

        $this->view('ads/edit', ['ad' => $ad, 'categories' => $categories, 'userInfo' => $user]);
    }

    public function update($id)
    {
        $this->checkAuth();

        $userId = $_SESSION[SESSION_NAME]['id'];
        $data = [
            'title'       => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price'       => $_POST['price'] ?? '',
            'category_id' => $_POST['category_id'] ?? '',
        ];

        // Atualiza os dados principais
        $this->adModel->update($id, $userId, $data);

        // Faz upload das novas imagens se existirem
        if (!empty($_FILES['images']['name'][0])) {
            $this->adModel->uploadImages($id, $_FILES['images']);
        }

        $_SESSION['success'] = 'Anúncio atualizado com sucesso!';
        header('Location: ' . BASE_URL . '/user/profile/ads');
        exit;
    }

    public function search()
    {
        $term = $_GET['term'] ?? '';
        $term = trim(strip_tags($term));
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        //$adModel = new \App\Models\Ad();
        $ads = $this->adModel->searchAds($term, $limit, $offset);

        $this->view('ads/search_results', ['ads' => $ads]);
    }

}
