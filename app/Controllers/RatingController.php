<?php
namespace App\Controllers;

use App\Models\AdRating;
use Core\Controller;

class RatingController extends Controller
{
    public function vote()
    {
        $user = $_SESSION[SESSION_NAME] ?? null;

        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'Login obrigatório']);
            return;
        }

        $userId = $user['id'];
        $adId = $_POST['ad_id'] ?? null;
        $rating = $_POST['rating'] ?? null;

        if ($adId === null || $rating === null || $rating < 0 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            return;
        }

        $ratingModel = new AdRating();
        $ratingModel->save($userId, $adId, $rating);
        $average = $ratingModel->getAverageRating($adId);

        echo json_encode([
            'success' => true,
            'average' => $average
        ]);
    }
}
