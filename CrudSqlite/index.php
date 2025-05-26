<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Inicia a sessão
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Se não estiver logado, redireciona para a página de login
    exit();
}

require_once __DIR__ . '/conexao.php';

echo "<h1>Estou no index.php após o login!</h1>"; 

// Obter categorias para o dropdown
$stmt_cat = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome');
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Obter status para o dropdown
$stmt_status = $pdo->query('SELECT id, nome FROM status_tarefas ORDER BY nome');
$status_tarefas = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/lista.css"> <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>📋 To-Do List</title>
    <style>
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f8f8;
            border-bottom: 1px solid #eee;
        }
        .user-info {
            font-weight: bold;
            color: #333;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
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
        .error-message {
            color: red;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header-top">
        <div class="user-info">Olá, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <section>
        <div class="centro">
            <div class="titulo">
                <h1>Adicionar Nova Tarefa</h1>
            </div>
            <form action="criar.php" method="post">
                <label for="nome_tarefa">Nome da Tarefa:</label>
                <input type="text" name="nome_tarefa" id="nome_tarefa" required>

                <label for="descricao_tarefa">Descrição (Opcional):</label>
                <textarea name="descricao_tarefa" id="descricao_tarefa" rows="3"></textarea>

                <label for="id_categoria">Categoria:</label>
                <select name="id_categoria" id="id_categoria" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nome']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="id_status">Status:</label>
                <select name="id_status" id="id_status" required>
                    <?php foreach ($status_tarefas as $status): ?>
                        <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['nome']); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="centralizar-botao">
                    <button name="submit" type="submit">Adicionar Tarefa</button>
                </div>
            </form>
        </div>
    </section>

    <section>
        <div class="centro">
            <div class="titulo">
                <h1>Minhas Tarefas</h1>
            </div>
            <?php require_once __DIR__ . '/listar.php'; ?>
        </div>
    </section>

    <script src="js/index.js"></script>
</body>
</html>