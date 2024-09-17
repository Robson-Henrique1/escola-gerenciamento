# Sistema de Gerenciamento de Usuários, Escolas, Professores e Alunos

Este projeto é um sistema de gerenciamento de usuários, escolas, professores e alunos, desenvolvido como parte de um desafio para estagiário. A aplicação utiliza **CodeIgniter 4** no backend, com autenticação baseada em **JWT** e um banco de dados **MySQL**.

## 🚀 Tecnologias

- **Backend:** CodeIgniter 4
- **Autenticação:** JWT
- **Banco de Dados:** MySQL
- **ORM:** Eloquent (ou outro ORM de sua escolha)

## 📋 Funcionalidades

- **Cadastro e Login**: Usuários podem se cadastrar e fazer login com CPF e senha.
- **Gerenciamento de Usuários**: Admins podem criar, visualizar, atualizar e excluir usuários.
- **Gerenciamento de Alunos**: Admins podem criar, visualizar, atualizar e excluir alunos.
- **Gerenciamento de Professores**: Admins podem criar, visualizar, atualizar e excluir professores.
- **Gerenciamento de Escolas**: Admins podem criar, visualizar, atualizar e excluir escolas.
- **Autenticação JWT**: Protege as rotas da API e gerencia sessões de usuário.

## 📬 Endpoints

### Usuários

| Método  | Endpoint          | Descrição                                          |
|---------|-------------------|--------------------------------------------------|
| `POST`  | `/api/usuarios`    | Cria um novo usuário (Administrador ou Professor) |

### Alunos

| Método  | Endpoint         | Descrição                                          |
|---------|------------------|--------------------------------------------------|
| `POST`  | `/api/alunos`     | Cria um novo aluno                                |
| `GET`   | `/api/alunos`     | Retorna a lista de todos os alunos                |
| `GET`   | `/api/alunos/{id}`| Retorna detalhes de um aluno específico           |
| `PUT`   | `/api/alunos/{id}`| Atualiza os dados de um aluno                     |
| `DELETE`| `/api/alunos/{id}`| Deleta um aluno                                   |

### Professores

| Método  | Endpoint            | Descrição                                          |
|---------|---------------------|--------------------------------------------------|
| `POST`  | `/api/professores`   | Cria um novo professor                            |
| `GET`   | `/api/professores`   | Retorna a lista de todos os professores           |
| `GET`   | `/api/professores/{id}`| Retorna detalhes de um professor específico     |
| `PUT`   | `/api/professores/{id}`| Atualiza os dados de um professor               |
| `DELETE`| `/api/professores/{id}`| Deleta um professor                             |

### Escolas

| Método  | Endpoint         | Descrição                                          |
|---------|------------------|--------------------------------------------------|
| `POST`  | `/api/escolas`    | Cria uma nova escola                              |
| `GET`   | `/api/escolas`    | Retorna a lista de todas as escolas               |
| `GET`   | `/api/escolas/{id}`| Retorna detalhes de uma escola específica        |
| `PUT`   | `/api/escolas/{id}`| Atualiza os dados de uma escola                  |
| `DELETE`| `/api/escolas/{id}`| Deleta uma escola                                |

## 🔑 Autenticação

A autenticação é gerenciada por JWT. Após o login, um token JWT é gerado e deve ser incluído no cabeçalho `Authorization` das requisições subsequentes.
