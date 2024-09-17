<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário - Sistema de Gerenciamento</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>
    <form action="<?= base_url('usuarios/cadastrar') ?>" method="post">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        <br>
        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" required>
        <br>
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>
        <br>
        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" name="data_nascimento" id="data_nascimento" required>
        <br>
        <button type="submit">Cadastrar</button>
    </form>
    <!-- Exibição de erros -->
    <?php if(session()->getFlashdata('error')): ?>
        <p><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>
</body>
</html>
