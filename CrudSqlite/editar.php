<?php
session_start(); // Inicia a sessão
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/conexao.php';

$tarefa_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Redireciona se o ID não for válido ou não for fornecido
if (!$tarefa_id) {
    header('Location: index.php');
    exit();
}

// 1. Lógica para carregar os dados da tarefa para edição
$current_task = null;
try {
    $sql_select = 'SELECT id, nome, descricao, id_categoria, id_status FROM tarefas WHERE id = :id AND id_usuario = :user_id';
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindValue(':id', $tarefa_id);
    $stmt_select->bindValue(':user_id', $_SESSION['user_id']); // Garante que só edita tarefas do próprio usuário
    $stmt_select->execute();
    $current_task = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$current_task) {
        echo "<script>alert('Tarefa não encontrada ou você não tem permissão para editá-la.'); window.location.href = 'index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>alert('Erro ao carregar tarefa: " . $e->getMessage() . "'); window.location.href = 'index.php';</script>";
    exit();
}

// Obter categorias para o dropdown
$stmt_cat = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome');
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Obter status para o dropdown
$stmt_status = $pdo->query('SELECT id, nome FROM status_tarefas ORDER BY nome');
$status_tarefas = $stmt_status->fetchAll(PDO::FETCH_ASSOC);


// 2. Lógica para processar a atualização da tarefa
if (isset($_POST['submit'])) {
    $novo_nome = filter_input(INPUT_POST, 'nome_tarefa', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nova_descricao = filter_input(INPUT_POST, 'descricao_tarefa', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nova_id_categoria = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
    $nova_id_status = filter_input(INPUT_POST, 'id_status', FILTER_VALIDATE_INT);
    $user_id_session = $_SESSION['user_id'];

    if (empty($novo_nome) || empty($nova_id_categoria) || empty($nova_id_status)) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios (Nome, Categoria, Status).');</script>";
    } else {
        try {
            $sql_update = 'UPDATE tarefas SET nome = :nome, descricao = :descricao, id_categoria = :id_categoria, id_status = :id_status WHERE id = :id AND id_usuario = :user_id';
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindValue(':nome', $novo_nome);
            $stmt_update->bindValue(':descricao', $nova_descricao);
            $stmt_update->bindValue(':id_categoria', $nova_id_categoria);
            $stmt_update->bindValue(':id_status', $nova_id_status);
            $stmt_update->bindValue(':id', $tarefa_id);
            $stmt_update->bindValue(':user_id', $user_id_session); // Garante que só edita tarefas do próprio usuário
            $result_update = $stmt_update->execute();

            if ($result_update) {
                // Opcional: Registrar log de atualização de tarefa
                $log_sql = 'INSERT INTO logs_sistema (id_usuario, acao) VALUES (:id_usuario, :acao)';
                $log_stmt = $pdo->prepare($log_sql);
                $log_stmt->bindValue(':id_usuario', $user_id_session);
                $log_stmt->bindValue(':acao', 'Tarefa "' . $novo_nome . '" (ID: ' . $tarefa_id . ') atualizada');
                $log_stmt->execute();

                echo "<script>alert('Tarefa atualizada com sucesso!'); window.location.href = 'index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Erro ao atualizar tarefa. Nenhuma alteração realizada.');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Erro ao atualizar tarefa: " . $e->getMessage() . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>📋 Edita | To-Do List</title>
    <style>
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form textarea,
        form select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .centralizar-botao {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<section>
    <div class="centro">
        <div class="titulo">
            <h1>Atualizar Tarefa</h1>
        </div>
        <form action="" method="post"> <label for="nome_tarefa">Nome da Tarefa:</label>
            <input type="text" name="nome_tarefa" id="nome_tarefa" value="<?php echo htmlspecialchars($current_task['nome'] ?? ''); ?>" required>

            <label for="descricao_tarefa">Descrição (Opcional):</label>
            <textarea name="descricao_tarefa" id="descricao_tarefa" rows="3"><?php echo htmlspecialchars($current_task['descricao'] ?? ''); ?></textarea>

            <label for="id_categoria">Categoria:</label>
            <select name="id_categoria" id="id_categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>" <?php echo (isset($current_task['id_categoria']) && $current_task['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_status">Status:</label>
            <select name="id_status" id="id_status" required>
                <?php foreach ($status_tarefas as $status): ?>
                    <option value="<?php echo $status['id']; ?>" <?php echo (isset($current_task['id_status']) && $current_task['id_status'] == $status['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($status['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="centralizar-botao">
                <button name="submit" type="submit">Atualizar</button>
            </div>
        </form>
    </div>
</section>
</body>
</html>