<?php
session_start();
session_destroy(); // Destrói todas as variáveis de sessão
header('Location: login.php'); // Redireciona para a página de login
exit();
?>