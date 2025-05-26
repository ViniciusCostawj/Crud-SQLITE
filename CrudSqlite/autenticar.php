<?php
session_start(); // Inicia a sessão para armazenar o status de login
require_once __DIR__ . '/conexao.php'; // Conecta ao banco de dados (Apenas inclui, não duplica o código)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1');
        exit();
    }

    $sql = 'SELECT id, username, password FROM usuarios WHERE username = :username';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login bem-sucedido
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Opcional: Registrar log de login
        $log_sql = 'INSERT INTO logs_sistema (id_usuario, acao) VALUES (:id_usuario, :acao)';
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->bindValue(':id_usuario', $user['id']);
        $log_stmt->bindValue(':acao', 'Login de usuário ' . $user['username']);
        $log_stmt->execute();

        header('Location: index.php'); // Redireciona para a página principal
        exit();
    } else {
        // Login falhou
        header('Location: login.php?error=1');
        exit();
    }
} else {
    // Se a requisição não for POST, redireciona para a página de login
    header('Location: login.php');
    exit();
}
?>