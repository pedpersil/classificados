<?php
namespace App\Models;

use Config\Database;
use PDO;

class Ad
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getByUser($userId)
    {
        $sql = "SELECT * FROM ads WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $categoryId, $title, $description, $price)
    {
        $sql = "INSERT INTO ads (user_id, category_id, title, description, price, created_at) 
                VALUES (:user_id, :category_id, :title, :description, :price, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':category_id' => $categoryId,
            ':title' => $title,
            ':description' => $description,
            ':price' => $price
        ]);
        return $this->pdo->lastInsertId();
    }

    public function uploadImages($adId, $files)
    {
        $uploadDir = __DIR__ . '/../../public/assets/uploads/';
        foreach ($files['name'] as $index => $name) {
            $tmpName = $files['tmp_name'][$index];
            $fileName = uniqid() . '_' . basename($name);
            move_uploaded_file($tmpName, $uploadDir . $fileName);

            $path = 'assets/uploads/' . $fileName;
            $stmt = $this->pdo->prepare("INSERT INTO ad_images (ad_id, image_path) VALUES (:ad_id, :path)");
            $stmt->execute([':ad_id' => $adId, ':path' => $path]);
        }
    }

    public function toggleStatus($adId, $userId)
    {
        $stmt = $this->pdo->prepare("UPDATE ads SET status = IF(status='active','inactive','active') 
                                     WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $adId, ':user_id' => $userId]);
    }

    public function delete($id, $userId)
    {
        $this->pdo->beginTransaction();

        // Remove imagens vinculadas
        $stmtImg = $this->pdo->prepare("DELETE FROM ad_images WHERE ad_id = :id");
        $stmtImg->execute([':id' => $id]);

        // Agora remove o anúncio
        $stmt = $this->pdo->prepare("DELETE FROM ads WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);

        $this->pdo->commit();
    }

    public function getActiveAdsWithImage()
    {
        $sql = "
            SELECT a.id, a.title, a.price, a.status, a.created_at, 
                (SELECT image_path FROM ad_images WHERE ad_id = a.id LIMIT 1) AS image 
            FROM ads a 
            WHERE a.status = 'active'
            ORDER BY a.created_at DESC
            LIMIT 20
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "
            SELECT 
                a.*, 
                u.name AS user_name, 
                u.email, 
                u.phone,
                u.address,
                u.state,
                u.zip_code,
                u.city,
                u.gender,
                u.photo_url,
                u.user_type,
                u.role,
                u.status AS user_status,
                u.created_at AS user_created_at,
                u.updated_at AS user_updated_at,
                (SELECT GROUP_CONCAT(image_path) FROM ad_images WHERE ad_id = a.id) AS images,
                
                c.id AS category_id,
                c.parent_id AS category_parent_id,
                c.name AS category_name,
                c.keywords AS category_keywords,

                cp.id AS parent_category_id,
                cp.name AS parent_category_name,
                cp.keywords AS parent_category_keywords
            FROM ads a
            JOIN users u ON u.id = a.user_id
            JOIN categories c ON c.id = a.category_id
            LEFT JOIN categories cp ON cp.id = c.parent_id
            WHERE a.id = :id AND a.status = 'active'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $ad = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ad) {
            // Trata imagens
            $ad['images'] = !empty($ad['images']) ? explode(',', $ad['images']) : [];

            // Subarray da categoria
            $ad['category'] = [
                'id' => $ad['category_id'],
                'parent_id' => $ad['category_parent_id'],
                'name' => $ad['category_name'],
                'keywords' => $ad['category_keywords'],
            ];

            // Subarray da categoria pai (se existir)
            if (!empty($ad['parent_category_id'])) {
                $ad['parent_category'] = [
                    'id' => $ad['parent_category_id'],
                    'name' => $ad['parent_category_name'],
                    'keywords' => $ad['parent_category_keywords'],
                ];
            } else {
                $ad['parent_category'] = null;
            }

            // Limpa campos auxiliares
            unset(
                $ad['category_id'], $ad['category_parent_id'], $ad['category_name'], $ad['category_keywords'],
                $ad['parent_category_id'], $ad['parent_category_name'], $ad['parent_category_keywords']
            );
        }

        return $ad;
    }

    public function getByIdForEdit($id, $userId)
    {
        $sql = "SELECT * FROM ads WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $userId, $data)
    {
        $sql = "UPDATE ads SET title = :title, description = :description, price = :price, category_id = :category_id WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':price'       => $data['price'],
            ':category_id' => $data['category_id'],
            ':id'          => $id,
            ':user_id'     => $userId
        ]);
    }

    // Retorna os dados de uma imagem pelo ID
    public function getImageById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT ai.id, ai.ad_id, ai.image_path, a.user_id 
            FROM ad_images ai
            JOIN ads a ON a.id = ai.ad_id
            WHERE ai.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Exclui uma imagem pelo ID
    public function deleteImageById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM ad_images WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getByIdWithImages($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ads WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $ad = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ad) {
            $imgStmt = $this->pdo->prepare("SELECT id, image_path FROM ad_images WHERE ad_id = :id");
            $imgStmt->execute([':id' => $id]);
            $ad['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $ad;
    }

    // Método para pegar os anúncios com paginação
    public function getAdsWithPagination($limit, $offset):array
    {
        $sql = "
            SELECT 
                ads.*, 
                categories.name AS category_name,
                categories.slug AS category_slug,
                categories.icon_path AS category_icon,
                (
                    SELECT image_path 
                    FROM ad_images 
                    WHERE ad_images.ad_id = ads.id 
                    LIMIT 1
                ) AS image
            FROM ads
            LEFT JOIN categories ON categories.id = ads.category_id
            WHERE ads.status = 'active'
            ORDER BY ads.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para pegar os anúncios com paginação
    public function getAdsWithPaginationUserPanel($userId, $recordsPerPage, $offset)
    {
        // Modifica a consulta para filtrar os anúncios pelo userId
        $sql = "SELECT * FROM ads WHERE user_id = :user_id LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);  // Adiciona o bind do user_id
        $stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para contar o total de anúncios
    public function getTotalAds()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ads WHERE status = 'active'");
        return $stmt->fetchColumn();
    }

    public function searchAds($term, $limit, $offset)
    {
        // 1. Buscar os anúncios e suas categorias
        $sql = "
            SELECT ads.*, categories.name AS category_name, categories.keywords AS category_keywords
            FROM ads
            JOIN categories ON ads.category_id = categories.id
            WHERE ads.status = 'active' AND (ads.title LIKE :term OR ads.description LIKE :term)
            ORDER BY ads.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term', "%$term%");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Para cada anúncio, buscar as imagens associadas
        foreach ($ads as &$ad) {
            $adId = $ad['id'];
            $imgStmt = $this->pdo->prepare("SELECT * FROM ad_images WHERE ad_id = :ad_id");
            $imgStmt->bindValue(':ad_id', $adId, PDO::PARAM_INT);
            $imgStmt->execute();
            $ad['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $ads;
    }

    public function getAdsByIds($ids)
    {
        if (empty($ids)) return [];

        // Cria uma string de placeholders: ?, ?, ?, ...
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT * FROM ads WHERE id IN ($placeholders) AND status = 'active'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
