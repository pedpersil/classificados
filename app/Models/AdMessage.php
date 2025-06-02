<?php
namespace App\Models;

use Config\Database;
use PDO;

class AdMessage
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function create($adId, $userId, $message)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $createdAt = date('Y-m-d H:i:s');

        $sql = "INSERT INTO ad_messages (ad_id, user_id, message, created_at) 
                VALUES (:ad_id, :user_id, :message, :created_at)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':ad_id' => $adId,
            ':user_id' => $userId,
            ':message' => $message,
            ':created_at' => $createdAt
        ]);
    }

    public function getByAd($adId)
    {
        $sql = "
            SELECT m.*, u.name, u.photo_url 
            FROM ad_messages m 
            JOIN users u ON u.id = m.user_id 
            WHERE m.ad_id = :ad_id 
            ORDER BY m.created_at ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ad_id' => $adId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($messageId, $userId, $isAdmin = false)
    {
        $sql = $isAdmin
            ? "DELETE FROM ad_messages WHERE id = :id"
            : "DELETE FROM ad_messages WHERE id = :id AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        $params = [':id' => $messageId];
        if (!$isAdmin) {
            $params[':user_id'] = $userId;
        }

        return $stmt->execute($params);
    }

}
