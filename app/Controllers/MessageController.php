<?php
namespace App\Controllers;

use App\Models\AdMessage;
use Core\Controller;

class MessageController extends Controller
{
    public function store()
    {
        $user = $_SESSION[SESSION_NAME] ?? null;

        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'É necessário login.']);
            return;
        }

        $adId = $_POST['ad_id'] ?? null;
        $message = trim($_POST['message'] ?? '');

        if (!$adId || $message === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Mensagem inválida.']);
            return;
        }

        $msgModel = new AdMessage();
        $msgModel->create($adId, $user['id'], $message);

        echo json_encode(['success' => true]);
    }

    public function fetch($adId)
    {
        $msgModel = new AdMessage();
        $messages = $msgModel->getByAd($adId);
        echo json_encode($messages);
    }

    public function delete()
    {
        $user = $_SESSION[SESSION_NAME] ?? null;

        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'Login obrigatório.']);
            return;
        }

        $messageId = $_POST['message_id'] ?? null;

        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido.']);
            return;
        }

        $msgModel = new \App\Models\AdMessage();
        $isAdmin = $user['role'] === 'admin';
        $success = $msgModel->delete($messageId, $user['id'], $isAdmin);

        echo json_encode(['success' => $success]);
    }

}
