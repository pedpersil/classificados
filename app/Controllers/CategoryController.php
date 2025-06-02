<?php
namespace App\Controllers;

use Core\Controller;
use Config\Database;
use App\Models\Category;
use App\Models\User;

class CategoryController extends Controller
{
    private Category $categoryModel;

    private User $userModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->userModel = new User();
    }

    public function index()
    {
        $this->checkAdmin();
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $categories = $this->categoryModel->all();
        $this->view('categories/index', ['userInfo' => $user, 'categories' => $categories]);
    }

    public function createForm()
    {
        $this->checkAdmin();
        $user = $this->userModel->getById($_SESSION[SESSION_NAME]['id']);
        $parents = $this->categoryModel->getParents();
        $this->view('categories/create', ['userInfo' => $user, 'parents' => $parents]);
    }

    public function store()
    {
        $this->checkAdmin();
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $keywords = trim($_POST['keywords'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;

        // Upload do ícone
        $iconPath = null;
        if (!empty($_FILES['icon']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/icons/';
            $fileName = uniqid() . '_' . basename($_FILES['icon']['name']);
            $targetPath = $uploadDir . $fileName;

            $path = '/assets/icons/' . $fileName;

            if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetPath)) {
                $iconPath = $targetPath;
            }
        }

        $this->categoryModel->create([
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => $slug,
            'keywords' => $keywords,
            'icon_path' => $path
        ]);

        $_SESSION['success'] = 'Categoria criada com sucesso!';

        header('Location:'. BASE_URL .'/admin/categories');
        exit;
    }

    public function editForm($id)
    {
        $this->checkAdmin();
        $category = $this->categoryModel->find($id);
        $parents = $this->categoryModel->getParents();
        $this->view('categories/edit', ['category' => $category, 'parents' => $parents]);
    }

    public function update($id)
    {
        $this->checkAdmin();
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $keywords = trim($_POST['keywords'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;

        $category = $this->categoryModel->find($id);
        $iconPath = $category['icon_path'] ?? null;

        // Atualiza o ícone, se houver novo upload
        if (!empty($_FILES['icon']['name'])) {
            $uploadDir = 'public/assets/icons/';
            $fileName = uniqid() . '_' . basename($_FILES['icon']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetPath)) {
                // remove antigo se existir
                if ($iconPath && file_exists($iconPath)) {
                    unlink($iconPath);
                }
                $iconPath = $targetPath;
            }
        }

        $this->categoryModel->update($id, [
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => $slug,
            'keywords' => $keywords,
            'icon_path' => $iconPath
        ]);

        $_SESSION['success'] = 'Categoria atualizada com sucesso!';
        
        header('Location:'. BASE_URL .'/admin/categories');
        exit;
        
    }

    public function delete($id)
    {
        $this->checkAdmin();
        $category = $this->categoryModel->find($id);
        if ($category && $category['icon_path'] && file_exists($category['icon_path'])) {
            unlink($category['icon_path']);
        }

        $this->categoryModel->delete($id);
        
        $_SESSION['success'] = 'Categoria excluida com sucesso!';
        header('Location:'. BASE_URL .'/admin/categories');
        exit;
    }

    public function fetch()
    {
        // Pegando os parâmetros da requisição
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $recordsPerPage = isset($_GET['recordsPerPage']) ? (int)$_GET['recordsPerPage'] : 15;
        $offset = ($page - 1) * $recordsPerPage;

        // Consultar as categorias com paginação
        $db = (new Database())->connect();
        $query = "SELECT * FROM categories LIMIT :offset, :recordsPerPage";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':recordsPerPage', $recordsPerPage, \PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Contar o total de categorias
        $totalQuery = "SELECT COUNT(*) FROM categories";
        $totalStmt = $db->prepare($totalQuery);
        $totalStmt->execute();
        $totalCategories = $totalStmt->fetchColumn();
        $totalPages = ceil($totalCategories / $recordsPerPage);

        // Gerar o HTML da tabela de categorias
        $categoriesHtml = '';
        foreach ($categories as $cat) {
            $categoriesHtml .= "<tr>
                <td>{$cat['id']}</td>
                <td>{$cat['name']}</td>
                <td>{$cat['slug']}</td>
                <td>{$cat['keywords']}</td>
                <td>" . ($cat['parent_id'] ? $this->getParentCategoryName($cat['parent_id']) : '—') . "</td>
                <td><img src='" . BASE_URL . "/" . $cat['icon_path'] . "' width='40'></td>
                <td>
                    <a href='" . BASE_URL . "/admin/categories/edit/{$cat['id']}' class='btn btn-sm btn-warning'>
                        <i class='bi bi-pencil-square'></i> Editar
                    </a>
                    <a href='" . BASE_URL . "/admin/categories/delete/{$cat['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir esta categoria?\");'>
                        <i class='bi bi-trash'></i> Excluir
                    </a>
                </td>
            </tr>";
        }

        // Gerar a navegação da paginação (max 5 páginas por vez)
        $paginationHtml = '';
        $maxPagesToShow = 5;
        $startPage = max(1, $page - 2); // Página inicial
        $endPage = min($totalPages, $page + 2); // Página final

        if ($page > 1) {
            $paginationHtml .= "<li class='page-item'>
                <a class='page-link' href='#' data-page='1'>&lt;&lt;</a>
            </li>";
        }

        // Exibe as páginas de 1 a 5 conforme o cálculo de intervalo
        for ($i = $startPage; $i <= $endPage; $i++) {
            $paginationHtml .= "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                <a class='page-link' href='#' data-page='{$i}'>{$i}</a>
            </li>";
        }

        if ($page < $totalPages) {
            $paginationHtml .= "<li class='page-item'>
                <a class='page-link' href='#' data-page='{$totalPages}'>&gt;&gt;</a>
            </li>";
        }

        // Retornar os dados em formato JSON
        echo json_encode([
            'categoriesHtml' => $categoriesHtml,
            'paginationHtml' => $paginationHtml
        ]);
    }


    private function getParentCategoryName($parentId)
    {
        $this->checkAdmin();

        $db = (new Database())->connect();
        $query = "SELECT name FROM categories WHERE id = :parentId";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':parentId', $parentId, \PDO::PARAM_INT);
        $stmt->execute();
        $parent = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $parent ? $parent['name'] : '—';
    }

    private function isAdmin()
    {
        $protected = $this->userModel->protectedRoute('Admin');
        return isset($_SESSION[SESSION_NAME]) && $_SESSION[SESSION_NAME]['role'] === 'admin' && $protected;
    }

    private function checkAdmin()
    {
        if (!$this->isAdmin()) {
            header('Location:'. BASE_URL .'/');
            exit;
        }
    }

}
