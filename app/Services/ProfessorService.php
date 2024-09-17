<?php

namespace App\Services;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProfessorService
{
    protected $modelUsuario;
    protected $validation;

    public function __construct()
    {
        $this->modelUsuario = new UsuarioModel();
        $this->validation = Services::validation(); // Instância de validação
    }

    private function extrairPerfilDoToken(): ?string
    {
        $authHeader = Services::request()->getHeaderLine('Authorization');
        if ($authHeader) {
            // Extrai o token do header Authorization
            list(, $token) = explode(' ', $authHeader);
    
            // Decodifica o JWT (não esqueça de ajustar a chave de acordo com sua implementação)
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
            
            // Retorna o perfil extraído do token
            return $decoded->perfil ?? null;
        }
    
        return null; // Se não houver token ou perfil, retorna null
    }
    
    private function perfilEhAdministrador(): bool
    {
        // Checa se o perfil do token é de administrador (1)
        return $this->extrairPerfilDoToken() === '1';
    }
    
    

    public function cadastrarProfessor(array $data): array
    {

        if (!$this->perfilEhAdministrador()) {
            return $this->respostaInvalida('Você não tem permissão para cadastrar professores.', ResponseInterface::HTTP_FORBIDDEN);
        }

        $data['perfil_id'] = 2; // Define o profile_id como 2 para professores

        return $this->criarUsuario($data);
    }

    public function listarProfessores(): array
    { 

        try {
            $professores = $this->modelUsuario->pegarProfessores();
            
            if (empty($professores)) {
                return $this->respostaSemConteudo('Nenhum professor encontrado.', ResponseInterface::HTTP_NO_CONTENT);
            }

            return $this->respostaValida('Professores encontrados.', $professores);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao listar professores: ' . $e->getMessage());
            return $this->respostaErro('Erro ao listar professores.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    private function criarUsuario(array $data): array
    {
        if (!$this->validarDados($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $inseriId = $this->modelUsuario->inseriUsuario(
                $data['nome'],
                $data['cpf'],
                $data['senha'],
                $data['data_nascimento'],
                $data['escola_id'],
                $data['perfil_id'],
            );

            if ($inseriId) {
                return $this->respostaCriada('Usuário cadastrado com sucesso', ['id' => $inseriId], ResponseInterface::HTTP_CREATED);
            }

            return $this->respostaErro('Erro ao cadastrar o professor.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar inserir professor: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar cadastrar o professor.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function deletarProfessor(int $id): array
    {

        if (!$this->perfilEhAdministrador()) {
            return $this->respostaInvalida('Você não tem permissão para deletar professores.', ResponseInterface::HTTP_FORBIDDEN);
        }

        if ($id === null) {
            return $this->respostaInvalida('ID é obrigatório.', ResponseInterface::HTTP_BAD_REQUEST);
        }
    
        try {
            if ($this->modelUsuario->deletarProfessor($id)) {
                return $this->respostaValida('Professor deletado com sucesso.', [], ResponseInterface::HTTP_OK);
            }
    
            return $this->respostaInvalida('Professor não encontrado.', ResponseInterface::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar deletar professor: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar deletar o professor. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function atualizarProfessor(int $id, array $data): array
    {
        if (!$this->validarRegistro($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        try {
            // Verifica se o ID é válido e se os dados são fornecidos
            if ($id <= 0 || empty($data)) {
                return $this->respostaInvalida('ID inválido ou dados insuficientes para atualização.', ResponseInterface::HTTP_BAD_REQUEST);
            }
    
            // Recupera o professor existente para verificar o CPF
            $professorExistente = $this->modelUsuario->pegarProfessorPorId($id);
            if (!$professorExistente) {
                return $this->respostaInvalida('Professor não encontrado com o ID fornecido.', ResponseInterface::HTTP_NOT_FOUND);
            }
    
            // Verifica se o CPF é único ou se é o mesmo do professor existente
            if (isset($data['cpf']) && $data['cpf'] !== $professorExistente['cpf']) {
                $cpfExistente = $this->modelUsuario->where('cpf', $data['cpf'])->first();
                if ($cpfExistente) {
                    return $this->respostaInvalida('O CPF informado já está em uso por outro professor.', ResponseInterface::HTTP_BAD_REQUEST);
                }
            }
    
            // Atualiza o professor
            $atualizado = $this->modelUsuario->atualizarProfessor($id, $data);
            
            if ($atualizado) {
                // Verifica se houve realmente alguma alteração nos dados
                $professorAtualizado = $this->modelUsuario->pegarProfessorPorId($id);
                if ($professorAtualizado === $professorExistente) {
                    return $this->respostaValida('Nenhuma alteração foi feita no professor.', [], ResponseInterface::HTTP_OK);
                }
    
                return $this->respostaValida('Professor atualizado com sucesso.', [], ResponseInterface::HTTP_OK);
            }
    
            // Se nenhuma linha foi afetada e nenhum erro ocorreu, presume-se que os dados são idênticos
            if ($this->modelUsuario->db->affectedRows() === 0) {
                return $this->respostaValida('Nenhuma alteração foi feita no professor.', [], ResponseInterface::HTTP_OK);
            }
    
            return $this->respostaErro('Erro ao atualizar o professor.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar professor: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar atualizar o professor. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    

private function validarDados(array $data): bool
{
    // Definindo as regras de validação
    $validationRules = [
        'nome' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'O campo nome é obrigatório.',
                'min_length' => 'O campo nome deve ter no mínimo 3 caracteres.',
                'max_length' => 'O campo nome deve ter no máximo 100 caracteres.',
            ]
        ],
        'cpf' => [
            'rules'  => 'required|exact_length[11]|is_unique[usuarios.cpf]',
            'errors' => [
                'required'        => 'O campo CPF é obrigatório.',
                'exact_length'    => 'O CPF deve ter exatamente 11 dígitos.',
                'is_unique'       => 'Já existe um usuário com este CPF.',
            ]
        ],
        'senha' => [
            'rules'  => 'permit_empty|min_length[6]',
            'errors' => [
                'min_length' => 'A senha deve ter no mínimo 6 caracteres.',
            ]
        ],
        'data_nascimento' => [
            'rules'  => 'required|valid_date',
            'errors' => [
                'required'   => 'O campo data de nascimento é obrigatório.',
                'valid_date' => 'A data de nascimento fornecida é inválida.',
            ]
        ],

        'escola_id' => [
            'rules'  => 'required|integer',
            'errors' => [
                'required'   => 'O campo escola é obrigatório.',
                'integer'    => 'O campo escola_id deve ser um número inteiro.',
            ]
        ],
    ];

    // Se estiver ignorando o CPF do usuário atual, ajuste a regra de is_unique

    // Define as regras de validação e executa a validação
    $this->validation->setRules($validationRules);

    // Executa a validação com os dados fornecidos
    return $this->validation->run($data);
}

private function validarRegistro(array $data): bool
{
    // Definindo as regras de validação
    $validationRules = [
        'nome' => [
            'rules'  => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required'   => 'O campo nome é obrigatório.',
                'min_length' => 'O campo nome deve ter no mínimo 3 caracteres.',
                'max_length' => 'O campo nome deve ter no máximo 100 caracteres.',
            ]
        ],
        'cpf' => [
            'rules'  => 'required|exact_length[11]',
            'errors' => [
                'required'        => 'O campo CPF é obrigatório.',
                'exact_length'    => 'O CPF deve ter exatamente 11 dígitos.',
            ]
        ],
        'senha' => [
            'rules'  => 'permit_empty|min_length[6]',
            'errors' => [
                'min_length' => 'A senha deve ter no mínimo 6 caracteres.',
            ]
        ],
        'data_nascimento' => [
            'rules'  => 'required|valid_date',
            'errors' => [
                'required'   => 'O campo data de nascimento é obrigatório.',
                'valid_date' => 'A data de nascimento fornecida é inválida.',
            ]
        ],
    ];

    // Se estiver ignorando o CPF do usuário atual, ajuste a regra de is_unique

    // Define as regras de validação e executa a validação
    $this->validation->setRules($validationRules);

    // Executa a validação com os dados fornecidos
    return $this->validation->run($data);
}


    private function respostaCriada(string $mensagem, array $dados): array
    {
        return [
            'status' => true,
            'message' => $mensagem,
            'data' => $dados,
            'error' => false,
            'http_code' => ResponseInterface::HTTP_CREATED
        ];
    }

    private function respostaValida(string $mensagem, array $dados = [], int $httpCode = ResponseInterface::HTTP_OK): array
    {
        return [
            'status' => true,
            'message' => $mensagem,
            'data' => $dados,
            'http_code' => $httpCode
        ];
    }

    private function respostaSemConteudo(string $mensagem): array
    {
        return [
            'status' => false,
            'message' => $mensagem,
            'data' => [],
            'error' => false,
            'http_code' => ResponseInterface::HTTP_NO_CONTENT
        ];
    }

    private function respostaErro(string $mensagem, int $httpCode = ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, string $error = ''): array
    {
        return [
            'status' => false,
            'message' => $mensagem,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }

    private function respostaInvalida($mensagem, int $httpCode = ResponseInterface::HTTP_BAD_REQUEST): array
    {
        return [
            'status' => false,
            'message' => $mensagem,
            'http_code' => $httpCode
        ];
    }
}
