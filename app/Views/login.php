<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Gerenciamento</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>"> <!-- Exemplo de CSS externo -->
</head>
<body>
    <h1>Login</h1>
    <form action="<?= base_url('login/autenticar') ?>" method="post">
        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" required>
        <br>
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>
        <br>
        <button type="submit">Entrar</button>
    </form>
    <!-- Exibição de erros -->
    <?php if(session()->getFlashdata('error')): ?>
        <p><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>
</body>
</html>
