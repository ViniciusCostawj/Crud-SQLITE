<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$senha_digitada = 'admin'; // A senha que você digitaria no formulário
// Copie este hash EXATAMENTE como ele aparece na sua saída do sqlite3 para o usuário 'admin'
$hash_do_banco = '$2y$10$7M0YQ3sF0v0D0D0D0D0D0u.Qp.oG1j2.q3.q4.q5.q6.q7.q8.q9.';

echo "Senha Digitada (variavel PHP): '" . htmlspecialchars($senha_digitada) . "'<br>";
echo "Hash do Banco (variavel PHP): '" . htmlspecialchars($hash_do_banco) . "'<br><br>";

echo "strlen(\$senha_digitada): " . strlen($senha_digitada) . "<br>";
echo "md5(\$senha_digitada): " . md5($senha_digitada) . "<br>"; // Para verificar se é o 'admin' esperado
echo "strlen(\$hash_do_banco): " . strlen($hash_do_banco) . "<br>";
echo "md5(\$hash_do_banco): " . md5($hash_do_banco) . "<br><br>"; // Para verificar o hash

if (password_verify($senha_digitada, $hash_do_banco)) {
    echo "<h2>password_verify() FUNCIONOU com o hash do banco!</h2>";
} else {
    echo "<h2>password_verify() FALHOU com o hash do banco!</h2>";
}

echo "<br><hr><br>"; // Separador

// Teste com um hash gerado AGORA para 'admin'
$novo_hash = password_hash('admin', PASSWORD_DEFAULT);
echo "Senha Digitada (para novo hash): '" . htmlspecialchars($senha_digitada) . "'<br>";
echo "Hash gerado agora: '" . htmlspecialchars($novo_hash) . "'<br><br>";

echo "strlen(\$novo_hash): " . strlen($novo_hash) . "<br>";
echo "md5(\$novo_hash): " . md5($novo_hash) . "<br><br>";

if (password_verify($senha_digitada, $novo_hash)) {
    echo "<h2>password_verify() FUNCIONOU com um hash recém-gerado!</h2>";
} else {
    echo "<h2>password_verify() FALHOU com um hash recém-gerado!</h2>";
}
?>