Sistema de Gerenciamento de Escolas e Usuários
Este projeto é uma API desenvolvida com CodeIgniter 4 no backend e React no frontend, com o objetivo de gerenciar usuários, escolas, professores, e alunos. Ele utiliza JWT para autenticação, além de integração com um banco de dados MySQL. Professores podem gerenciar alunos, enquanto administradores têm permissões mais amplas para gerenciar todas as entidades.

📋 Sumário
Resumo
Tecnologias Utilizadas
Instalação
Configuração
Endpoints
Autenticação JWT
Contribuição
📄 Resumo
Esta API permite o gerenciamento de alunos, professores, escolas e usuários do sistema, com permissões baseadas em papéis. Professores só podem ver e gerenciar alunos, enquanto administradores têm controle total sobre todos os recursos.

As principais funcionalidades incluem:

Cadastro de usuários, alunos, professores e escolas.
Sistema de login com autenticação via CPF e senha.
Autenticação via JWT com permissões definidas por papéis.
Exibição de listas de alunos, professores e escolas.
🚀 Tecnologias Utilizadas
Backend: CodeIgniter 4
Frontend: React.js
Autenticação: JSON Web Token (JWT)
Banco de Dados: MySQL
ORM: Utilizado para facilitar as interações com o banco de dados

📬 Endpoints
Usuários
Método	Endpoint	Descrição
POST	/api/usuarios	Cria um novo usuário (Administrador ou Professor)
GET	/api/usuarios	Retorna a lista de todos os usuários
GET	/api/usuarios/{id}	Retorna detalhes de um usuário específico
PUT	/api/usuarios/{id}	Atualiza os dados de um usuário
DELETE	/api/usuarios/{id}	Deleta um usuário
Alunos
Método	Endpoint	Descrição
POST	/api/alunos	Cria um novo aluno
GET	/api/alunos	Retorna a lista de todos os alunos
GET	/api/alunos/{id}	Retorna detalhes de um aluno específico
PUT	/api/alunos/{id}	Atualiza os dados de um aluno
DELETE	/api/alunos/{id}	Deleta um aluno
Professores
Método	Endpoint	Descrição
POST	/api/professores	Cria um novo professor
GET	/api/professores	Retorna a lista de todos os professores
GET	/api/professores/{id}	Retorna detalhes de um professor específico
PUT	/api/professores/{id}	Atualiza os dados de um professor
DELETE	/api/professores/{id}	Deleta um professor
Escolas
Método	Endpoint	Descrição
POST	/api/escolas	Cria uma nova escola
GET	/api/escolas	Retorna a lista de todas as escolas
GET	/api/escolas/{id}	Retorna detalhes de uma escola específica
PUT	/api/escolas/{id}	Atualiza os dados de uma escola
DELETE	/api/escolas/{id}	Deleta uma escola
Esses endpoints cobrem todas as operações básicas de CRUD para usuários, alunos, professores e escolas. Para cada recurso, é possível criar, ler, atualizar e deletar registros.

Os endpoints devem ser autenticados com JWT, e o token deve ser enviado no cabeçalho da requisição com o formato:
