<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Ad;
use App\Models\User;

class HomeController extends Controller
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
        $ads = $this->adModel->getActiveAdsWithImage();

        $recentAds = [];
        if (!empty($_SESSION['recent_ads'])) {
            $recentAds = $this->adModel->getAdsByIds($_SESSION['recent_ads']);
        }

        if (isset($_SESSION[SESSION_NAME])) {
            $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
            $this->view('home/index', ['ads' => $ads, 'userInfo' => $user, 'recentAds' => $recentAds]);
        }

        $this->view('home/index', ['ads' => $ads, 'recentAds' => $recentAds]);
        
    }

    public function about()
    {
        
        if (isset($_SESSION[SESSION_NAME])) {
            $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
            $this->view('home/about', ['userInfo' => $user]);
        }

        $this->view('home/about');
        
    }

    public function privacyPolicy()
    {

        if (isset($_SESSION[SESSION_NAME])) {
            $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
            $this->view('home/privacy_policy', ['userInfo' => $user]);
        }

        $this->view('home/privacy_policy');
        
    }

    public function contact()
    {
       header('Location: https://pedrosilva.tech/#contact');
       exit; 
    }
}
