<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Escola - Sistema de Gerenciamento</title>
</head>
<body>
    <h1>Cadastro de Escola</h1>
    <form action="<?= base_url('escolas/cadastrar') ?>" method="post">
        <label for="nome">Nome da Escola:</label>
        <input type="text" name="nome" id="nome" required>
        <br>
        <label for="endereco">Endereço:</label>
        <input type="text" name="endereco" id="endereco" required>
        <br>
        <button type="submit">Cadastrar Escola</button>
    </form>
</body>
</html>
