<?php
session_start(); // Inicia a sessão
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/conexao.php';

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id']; // ID do usuário logado

    if (!$id) {
        echo "<script>alert('ID da tarefa inválido.'); window.location.href = 'index.php';</script>";
        exit();
    }

    try {
        // Garante que o usuário só pode excluir suas próprias tarefas
        $sql = 'DELETE FROM tarefas WHERE id = :id AND id_usuario = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':user_id', $user_id);
        $result = $stmt->execute();

        if ($result && $stmt->rowCount() > 0) { // Verifica se alguma linha foi realmente deletada
            // Opcional: Registrar log de exclusão de tarefa
            $log_sql = 'INSERT INTO logs_sistema (id_usuario, acao) VALUES (:id_usuario, :acao)';
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->bindValue(':id_usuario', $user_id);
            $log_stmt->bindValue(':acao', 'Tarefa (ID: ' . $id . ') excluída');
            $log_stmt->execute();

            echo "<script>alert('Tarefa excluída com sucesso!'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Tarefa não encontrada ou você não tem permissão para excluí-la.'); window.location.href = 'index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao excluir tarefa: " . $e->getMessage() . "'); window.location.href = 'index.php';</script>";
    }
} else {
    echo "<script>alert('Id da tarefa não fornecido.'); window.location.href = 'index.php';</script>";
}
?>