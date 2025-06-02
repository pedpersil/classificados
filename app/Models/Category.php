<?php
namespace App\Models;

use Config\Database;
use PDO;

class Category
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getParents()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO categories (parent_id, name, slug, keywords, icon_path) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['parent_id'] ?: null,
            $data['name'],
            $data['slug'],
            $data['keywords'],
            $data['icon_path'] ?? null
        ]);
    }

    public function update(int $id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE categories SET parent_id = ?, name = ?, slug = ?, keywords = ?, icon_path = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([
            $data['parent_id'] ?: null,
            $data['name'],
            $data['slug'],
            $data['keywords'],
            $data['icon_path'] ?? null,
            $id
        ]);
    }

    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
