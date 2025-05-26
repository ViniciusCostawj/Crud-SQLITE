<?php
// Não precisa de session_start() ou verificação de login aqui, pois é incluído por index.php que já faz isso.
// require_once __DIR__ . '/conexao.php'; // Remova ou comente se index.php já faz isso

// Conexão já feita em index.php
// $pdo é acessível aqui

// Consulta as tarefas, juntando com categorias, status e usuários para exibir os nomes
$sql = 'SELECT t.id, t.nome, t.descricao, c.nome as categoria_nome, s.nome as status_nome, u.username
        FROM tarefas t
        LEFT JOIN categorias c ON t.id_categoria = c.id
        LEFT JOIN status_tarefas s ON t.id_status = s.id
        LEFT JOIN usuarios u ON t.id_usuario = u.id
        WHERE t.id_usuario = :user_id -- Filtra as tarefas pelo usuário logado
        ORDER BY t.data_criacao DESC';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id']); // Usa o ID do usuário logado
$stmt->execute();
$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Variável renomeada para $tarefas

?>

<?php if (empty($tarefas)): ?>
    <p style="text-align: center; color: #555;">Nenhuma tarefa encontrada. Adicione uma nova!</p>
<?php else: ?>
    <?php foreach($tarefas as $tarefa): ?>
        <div class="tarefas"> <div class="conteudo">
                <input type="checkbox" name="tarefa_concluida" id="tarefa_<?= $tarefa['id']; ?>" onchange="marcatarefas(this)"> <label for="tarefa_<?= $tarefa['id']; ?>">
                    **<?php echo htmlspecialchars($tarefa['nome']); ?>**<br>
                    <?php if (!empty($tarefa['descricao'])): ?>
                        <small>Descrição: <?php echo htmlspecialchars($tarefa['descricao']); ?></small><br>
                    <?php endif; ?>
                    <small>Categoria: <?php echo htmlspecialchars($tarefa['categoria_nome'] ?? 'N/A'); ?></small><br>
                    <small>Status: <?php echo htmlspecialchars($tarefa['status_nome'] ?? 'N/A'); ?></small><br>
                    <small>Criado por: <?php echo htmlspecialchars($tarefa['username'] ?? 'Desconhecido'); ?></small>
                </label>
            </div>
            <div class="links">
                <div class="link-1">
                    <a href="editar.php?id=<?= $tarefa['id']; ?>"><i class="material-icons">create</i></a>
                </div>
                <div class="link-2">
                    <a href="excluir.php?id=<?= $tarefa['id']; ?>"><i class="material-icons">delete</i></a>
                </div>
            </div>
        </div>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

<script>
// A função marcatarefas precisa ser definida em js/index.js ou aqui, se não for global
function marcatarefas(checkbox) {
    var label = checkbox.nextElementSibling; // Pega o label associado ao checkbox
    if (checkbox.checked) {
        label.style.textDecoration = 'line-through';
        label.style.color = '#888';
    } else {
        label.style.textDecoration = 'none';
        label.style.color = '#333';
    }
}
</script>