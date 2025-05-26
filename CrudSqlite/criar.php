<?php
session_start(); // Inicia a sessão
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/conexao.php';

if (isset($_POST['submit'])) {
    $nome_tarefa = filter_input(INPUT_POST, 'nome_tarefa', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $descricao_tarefa = filter_input(INPUT_POST, 'descricao_tarefa', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id_categoria = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
    $id_status = filter_input(INPUT_POST, 'id_status', FILTER_VALIDATE_INT);
    $id_usuario = $_SESSION['user_id']; // Pega o ID do usuário logado

    if (empty($nome_tarefa) || empty($id_categoria) || empty($id_status)) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios (Nome, Categoria, Status).'); window.location.href = 'index.php';</script>";
        exit();
    }

    $sql = 'INSERT INTO tarefas (nome, descricao, id_usuario, id_categoria, id_status) VALUES (:nome, :descricao, :id_usuario, :id_categoria, :id_status)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome_tarefa);
    $stmt->bindValue(':descricao', $descricao_tarefa);
    $stmt->bindValue(':id_usuario', $id_usuario);
    $stmt->bindValue(':id_categoria', $id_categoria);
    $stmt->bindValue(':id_status', $id_status);

    $result = $stmt->execute();

    if ($result) {
        // Opcional: Registrar log de criação de tarefa
        $log_sql = 'INSERT INTO logs_sistema (id_usuario, acao) VALUES (:id_usuario, :acao)';
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->bindValue(':id_usuario', $id_usuario);
        $log_stmt->bindValue(':acao', 'Tarefa "' . $nome_tarefa . '" criada');
        $log_stmt->execute();

        echo "<script>alert('Tarefa adicionada com sucesso!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erro ao adicionar tarefa.'); window.location.href = 'index.php';</script>";
    }
} else {
    header('Location: index.php'); // Redireciona se não for POST
    exit();
}
?>