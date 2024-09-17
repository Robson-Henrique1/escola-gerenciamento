<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno - Sistema de Gerenciamento</title>
</head>
<body>
    <h1>Cadastro de Aluno</h1>
    <form action="<?= base_url('alunos/cadastrar') ?>" method="post">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        <br>
        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" required>
        <br>
        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" name="data_nascimento" id="data_nascimento" required>
        <br>
        <label for="professor">Professor:</label>
        <select name="professor_id" id="professor" required>
            <!-- Exemplo de opções dinamicamente preenchidas -->
            <?php foreach ($professores as $professor): ?>
                <option value="<?= $professor['id'] ?>"><?= $professor['nome'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Cadastrar Aluno</button>
    </form>
</body>
</html>
