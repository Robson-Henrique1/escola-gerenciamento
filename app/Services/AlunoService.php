<?php

namespace App\Services;

use App\Models\AlunoModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;


class AlunoService
{
    protected $modelAluno;
    protected $validation;

    public function __construct()
    {
        $this->modelAluno = new AlunoModel();
        $this->validation = Services::validation(); // Obter a instância de validação
    }
    
    public function criarAluno(array $data): array
    {
        // Chama o método para cadastrar o aluno
        return $this->cadastrarAluno($data);
    }

    public function listarAlunos(): array
    {
        try {
            $alunos = $this->modelAluno->pegarAlunos();

            if (empty($alunos)) {
                return $this->respostaSemConteudo('Nenhum aluno encontrado.', ResponseInterface::HTTP_NO_CONTENT);
            }

            return $this->respostaCriada('Alunos encontrados.', $alunos);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao listar alunos: ' . $e->getMessage());
            return $this->respostaErro('Erro ao listar alunos.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    private function cadastrarAluno(array $data): array
    { 
        // Verifica a validade dos dados
        if (!$this->validarDados($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
    
        try {
            // Tenta inserir o aluno
            $inseriId = $this->modelAluno->inseriAluno(
                $data['nome'],
                $data['cpf'],
                $data['data_nascimento'],
                $data['professor_id']
            );
    
            if ($inseriId) {
                return $this->respostaCriada('Aluno cadastrado com sucesso', ['id' => $inseriId]);
            }
    
            return $this->respostaErro('Erro ao cadastrar o aluno.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Registra o erro e retorna uma resposta de erro
            log_message('error', 'Exception ao tentar inserir aluno: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar cadastrar o aluno.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function deletarAluno(int $id): array
    {
        if ($id === null) {
            return $this->respostaInvalida('ID é obrigatório.', ResponseInterface::HTTP_BAD_REQUEST);
        }
    
        try {
            if ($this->modelAluno->deletarAluno($id)) {
                return $this->respostaValida('Aluno deletado com sucesso.', [], ResponseInterface::HTTP_OK);
            }
    
            return $this->respostaInvalida('Aluno não encontrado.', ResponseInterface::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar deletar aluno: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar deletar o aluno. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function atualizarAluno(int $id, array $data): array
    {

        if (!$this->validarDadosAtualizar($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        try {
            // Verifica se o ID é válido e se os dados são fornecidos
            if ($id <= 0 || empty($data)) {
                return $this->respostaInvalida('ID inválido ou dados insuficientes para atualização.', ResponseInterface::HTTP_BAD_REQUEST);
            }

            $alunoExistente = $this->modelAluno->pegarAlunoPorId($id);
            if (!$alunoExistente) {
                return $this->respostaInvalida('Aluno não encontrado com o ID fornecido.', ResponseInterface::HTTP_NOT_FOUND);
            }
    
            // Verifica se o CPF é único ou se é o mesmo do aluno existente
            if (isset($data['cpf']) && $data['cpf'] !== $alunoExistente['cpf']) {
                $cpfExistente = $this->modelAluno->where('cpf', $data['cpf'])->first();
                if ($cpfExistente) {
                    return $this->respostaInvalida('O CPF informado já está em uso por outro aluno.', ResponseInterface::HTTP_BAD_REQUEST);
                }
            }
    
            // Atualiza o aluno
            $atualizado = $this->modelAluno->update($id, $data);
    
            if ($atualizado) {
                // Verifica se houve realmente alguma alteração nos dados
                $alunoAtualizado = $this->modelAluno->pegarAlunoPorId($id);
                if ($alunoAtualizado === $alunoExistente) {
                    return $this->respostaValida('Nenhuma alteração foi feita no aluno.', [], ResponseInterface::HTTP_OK);
                }
    
                return $this->respostaValida('Aluno atualizado com sucesso.', [], ResponseInterface::HTTP_OK);
            }

            return $this->respostaErro('Erro ao atualizar o aluno.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar aluno: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar atualizar o aluno. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    

    private function validarDados(array $data): bool
    {
        // Regras de validação em português
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
                'rules'  => 'required|exact_length[11]|is_unique[alunos.cpf]',
                'errors' => [
                    'required'        => 'O campo CPF é obrigatório.',
                    'exact_length'    => 'O CPF deve ter exatamente 11 dígitos.',
                    'is_unique'       => 'Já existe um usuário com este CPF.',
                ]
            ],
            'data_nascimento' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'O campo data de nascimento é obrigatório.',
                    'valid_date' => 'A data de nascimento fornecida é inválida.',
                ]
            ],
            'professor_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required'   => 'O campo professor é obrigatório.',
                    'integer'    => 'O campo professor_id deve ser um número inteiro.',
                ]
            ],
        ];

        $this->validation->setRules($validationRules);

        return $this->validation->run($data);
    }

    private function validarDadosAtualizar(array $data): bool
    {
        // Regras de validação em português
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
            'data_nascimento' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'O campo data de nascimento é obrigatório.',
                    'valid_date' => 'A data de nascimento fornecida é inválida.',
                ]
            ],
            'professor_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required'   => 'O campo professor é obrigatório.',
                    'integer'    => 'O campo professor_id deve ser um número inteiro.',
                ]
            ],
        ];

        $this->validation->setRules($validationRules);

        return $this->validation->run($data);
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
