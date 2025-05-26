<?php
// Defina as informações de conexão para SQLite
$databaseFile = __DIR__ . '/database.db';

try {
    // Cria uma instância PDO para SQLite
    $pdo = new PDO("sqlite:$databaseFile");
    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Opcional: Configura o modo de busca padrão para objetos ou array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Não há mensagem de sucesso aqui para evitar output antes dos headers

} catch (PDOException $e) {
    // Em um ambiente de produção, você deve logar o erro e mostrar uma mensagem genérica.
    // Para desenvolvimento, podemos mostrar o erro completo.
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}
// Não coloque nada aqui, nem mesmo a tag de fechamento PHP se for apenas PHP