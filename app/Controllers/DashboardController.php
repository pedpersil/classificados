<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $this->checkAuth();
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $this->view('dashboard/index', ['userInfo' => $user]);
    }

    private function isAdmin()
    {
        $protected = $this->userModel->protectedRoute('Admin');
        return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['role'] === 'admin' && $protected;
    }
    
    private function checkAuth()
    {
        if (!$this->isAdmin()) {
            header('Location:'. BASE_URL .'/admin');
            exit;
        }
    }
}
