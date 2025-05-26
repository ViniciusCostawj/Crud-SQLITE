<?php
$databaseFile = __DIR__ . '/database.db';

try {
    $pdo = new PDO('sqlite:' . $databaseFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Criar tabela de usuários
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Criar tabela de categorias
    $pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL UNIQUE
    )");

    // Criar tabela de status_tarefas
    $pdo->exec("CREATE TABLE IF NOT EXISTS status_tarefas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL UNIQUE
    )");

    // Criar tabela de tarefas
    $pdo->exec("CREATE TABLE IF NOT EXISTS tarefas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_usuario INTEGER NOT NULL,
        nome TEXT NOT NULL,
        descricao TEXT,
        id_categoria INTEGER NOT NULL,
        id_status INTEGER NOT NULL,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
        FOREIGN KEY (id_categoria) REFERENCES categorias(id),
        FOREIGN KEY (id_status) REFERENCES status_tarefas(id)
    )");

    // Criar tabela de logs_sistema
    $pdo->exec("CREATE TABLE IF NOT EXISTS logs_sistema (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_usuario INTEGER,
        acao TEXT NOT NULL,
        data_log DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
    )");

    // Inserir dados de teste se as tabelas estiverem vazias
    $stmt = $pdo->query('SELECT COUNT(*) FROM usuarios');
    if ($stmt->fetchColumn() == 0) {
        $adminPasswordHash = password_hash('admin', PASSWORD_DEFAULT); // Geração segura do hash para 'admin'
        $pdo->prepare('INSERT INTO usuarios (username, password) VALUES (?, ?)')->execute(['admin', $adminPasswordHash]);
        $pdo->prepare('INSERT INTO usuarios (username, password) VALUES (?, ?)')->execute(['usuario1', $adminPasswordHash]); 
        
        $pdo->prepare('INSERT INTO usuarios (username, password) VALUES (?, ?)')->execute(['usuario2', $adminPasswordHash]); 
        
    }

    $stmt = $pdo->query('SELECT COUNT(*) FROM categorias');
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO categorias (nome) VALUES ('Trabalho'), ('Pessoal'), ('Estudo'), ('Outros')");
    }

    $stmt = $pdo->query('SELECT COUNT(*) FROM status_tarefas');
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO status_tarefas (nome) VALUES ('Pendente'), ('Em Andamento'), ('Concluída'), ('Cancelada')");
    }

    echo "Banco de dados e tabelas criados/atualizados com sucesso e dados de teste inseridos!\n";

} catch (PDOException $e) {
    echo "Erro ao criar/atualizar o banco de dados: " . $e->getMessage() . "\n";
}
?>