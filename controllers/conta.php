<?php
/**
 * Controller: Minha Conta
 * Permite ao usuário logado editar nome, e-mail e alterar senha.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../helpers/functions.php';
verificarLogin();

$db        = Database::getInstance();
$userId    = $_SESSION['user_id'];
$companyId = getCompanyId();
$errors    = [];
$tab       = $_GET['tab'] ?? 'perfil';

// Buscar dados atuais do usuário
$usuario = $db->queryOne(
    "SELECT id, nome, email, perfil, data_criacao FROM users WHERE id = :id AND company_id = :cid",
    [':id' => $userId, ':cid' => $companyId]
);

if (!$usuario) {
    $_SESSION['error'] = 'Usuário não encontrado.';
    header('Location: ?page=dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // --- Atualizar perfil (nome + e-mail) ---
    if ($acao === 'perfil') {
        $nome  = sanitize($_POST['nome']  ?? '');
        $email = sanitize($_POST['email'] ?? '');

        if (empty($nome)) {
            $errors[] = 'O nome é obrigatório.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }

        if (empty($errors) && $email !== $usuario['email']) {
            $existente = $db->queryOne(
                "SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1",
                [':email' => $email, ':id' => $userId]
            );
            if ($existente) {
                $errors[] = 'Este e-mail já está em uso por outro usuário.';
            }
        }

        if (empty($errors)) {
            $db->execute(
                "UPDATE users SET nome = :nome, email = :email WHERE id = :id AND company_id = :cid",
                [':nome' => $nome, ':email' => $email, ':id' => $userId, ':cid' => $companyId]
            );
            $_SESSION['user_name']  = $nome;
            $_SESSION['user_email'] = $email;
            $_SESSION['success']    = 'Dados atualizados com sucesso.';
            header('Location: ?page=conta&tab=perfil');
            exit;
        }

        // Repopular com dados enviados em caso de erro
        $usuario['nome']  = $nome;
        $usuario['email'] = $email;
        $tab = 'perfil';
    }

    // --- Alterar senha ---
    if ($acao === 'senha') {
        $senhaAtual     = $_POST['senha_atual']     ?? '';
        $novaSenha      = $_POST['nova_senha']       ?? '';
        $confirmarSenha = $_POST['confirmar_senha']  ?? '';

        if (empty($senhaAtual)) {
            $errors[] = 'Informe a senha atual.';
        }
        if (strlen($novaSenha) < 8) {
            $errors[] = 'A nova senha deve ter no mínimo 8 caracteres.';
        }
        if ($novaSenha !== $confirmarSenha) {
            $errors[] = 'As senhas não coincidem.';
        }

        if (empty($errors)) {
            $row = $db->queryOne("SELECT senha FROM users WHERE id = :id", [':id' => $userId]);
            if (!$row || !password_verify($senhaAtual, $row['senha'])) {
                $errors[] = 'A senha atual está incorreta.';
            }
        }

        if (empty($errors)) {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $db->execute(
                "UPDATE users SET senha = :senha WHERE id = :id AND company_id = :cid",
                [':senha' => $hash, ':id' => $userId, ':cid' => $companyId]
            );
            $_SESSION['success'] = 'Senha alterada com sucesso.';
            header('Location: ?page=conta&tab=senha');
            exit;
        }

        $tab = 'senha';
    }
}

$page_title = 'Minha Conta';
ob_start();
include __DIR__ . '/../views/conta/index.php';
$content = ob_get_clean();
include __DIR__ . '/../views/template.php';
