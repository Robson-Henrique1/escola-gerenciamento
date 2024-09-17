<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Professor - Sistema de Gerenciamento</title>
</head>
<body>
    <h1>Cadastro de Professor</h1>
    <form action="<?= base_url('professores/cadastrar') ?>" method="post">
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
        <label for="escola">Escola:</label>
        <select name="escola_id" id="escola" required>
            <!-- Exemplo de opções dinamicamente preenchidas -->
            <?php foreach ($escolas as $escola): ?>
                <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Cadastrar Professor</button>
    </form>
</body>
</html>
