<?php
namespace App\Models;

use Config\Database;
use PDO;

class AdRating
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save($userId, $adId, $rating)
    {
        $sql = "
            INSERT INTO ad_ratings (user_id, ad_id, rating)
            VALUES (:user_id, :ad_id, :rating)
            ON DUPLICATE KEY UPDATE rating = :rating
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':ad_id' => $adId,
            ':rating' => $rating
        ]);
    }

    public function getUserRating($userId, $adId)
    {
        $sql = "SELECT rating FROM ad_ratings WHERE user_id = :user_id AND ad_id = :ad_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':ad_id' => $adId]);
        return $stmt->fetchColumn();
    }

    public function getAverageRating($adId)
    {
        $sql = "SELECT AVG(rating) FROM ad_ratings WHERE ad_id = :ad_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ad_id' => $adId]);
        $avg = $stmt->fetchColumn();

        return $avg !== null ? round((float) $avg, 1) : 0.0;
    }

}
