<?php

namespace App\Services;

use App\Models\EscolaModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EscolaService
{
    protected $modelEscola;
    protected $validation;
    
    public function __construct()
    {
        $this->modelEscola = new EscolaModel();
        $this->validation = Services::validation(); // Obter a instância de validação
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

    public function cadastrarEscola(array $data): array
    {
        return $this->criarEscola($data);
    }

    public function obterTodasEscolas(): array
    {
        try {
            $data = $this->modelEscola->findAll();
            
            if (empty($data)) {
                return $this->respostaSemConteudo('Nenhuma escola encontrada.');
            }

            return $this->respostaValida('Escolas encontradas com sucesso.', $data);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar obter escolas: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar obter as escolas.', $e->getMessage());
        }
    }

    private function criarEscola(array $data): array
    {
        if (!$this->perfilEhAdministrador()) {
            return $this->respostaInvalida('Você não tem permissão para cadastrar escolas.', ResponseInterface::HTTP_FORBIDDEN);
        }

        if (!$this->validarDados($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $inseriId = $this->modelEscola->inseriEscola($data['nome'], $data['endereco']);

            if ($inseriId) {
                $data['id'] = $inseriId;
                return $this->respostaCriada('Escola cadastrada com sucesso.', $data);
            }

            return $this->respostaErro('Erro ao cadastrar a escola.');
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar inserir escola: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar cadastrar a escola.', $e->getMessage());
        }
    }


    public function deletarEscola(int $id): array
    {
        if (!$this->perfilEhAdministrador()) {
            return $this->respostaInvalida('Você não tem permissão para deletar escolas.', ResponseInterface::HTTP_FORBIDDEN);
        }

        if ($id === null) {
            return $this->respostaInvalida('ID é obrigatório.', ResponseInterface::HTTP_BAD_REQUEST);
        }
    
        try {
            if ($this->modelEscola->deletarEscola($id)) {
                return $this->respostaValida('Escola deletado com sucesso.', [], ResponseInterface::HTTP_OK);
            }
    
            return $this->respostaInvalida('Escola não encontrado.', ResponseInterface::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar deletar escola: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar deletar o escola. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function atualizarEscola(int $id, array $data): array
    {

        if (!$this->perfilEhAdministrador()) {
            return $this->respostaInvalida('Você não tem permissão para editar escolas.', ResponseInterface::HTTP_FORBIDDEN);
        }

        if (!$this->validarDados($data)) {
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        try {
            // Verifica se o ID é válido e se os dados são fornecidos
            if ($id <= 0 || empty($data)) {
                return $this->respostaInvalida('ID inválido ou dados insuficientes para atualização.', ResponseInterface::HTTP_BAD_REQUEST);
            }

            $escolaExistente = $this->modelEscola->pegarEscolaPorId($id);
            if (!$escolaExistente) {
                return $this->respostaInvalida('Escola não encontrado com o ID fornecido.', ResponseInterface::HTTP_NOT_FOUND);
            }
            
            // Atualiza o escola
            $atualizado = $this->modelEscola->atualizarEscola($id, $data);
            
            if ($atualizado) {
                // Verifica se houve realmente alguma alteração nos dados
                $escolaAtualizado = $this->modelEscola->pegarEscolaPorId($id);
                if ($escolaAtualizado === $escolaExistente) {
                    return $this->respostaValida('Nenhuma alteração foi feita no escola.', [], ResponseInterface::HTTP_OK);
                }
    
                return $this->respostaValida('Escola atualizado com sucesso.', [], ResponseInterface::HTTP_OK);
            }

            return $this->respostaErro('Erro ao atualizar o escola.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar escola: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar atualizar o escola. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    private function validarDados(array $data): bool
    {
        // Definindo as mensagens de erro personalizadas
        $validationRules = [
            'nome' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'O campo nome é obrigatório.',
                    'min_length' => 'O campo nome deve ter no mínimo 3 caracteres.',
                    'max_length' => 'O campo nome deve ter no máximo 100 caracteres.',
                ]
            ],
            'endereco' => [
                'rules'  => 'required|min_length[5]|max_length[100]',
                'errors' => [
                    'required'   => 'O campo endereço é obrigatório.',
                    'min_length' => 'O campo endereço deve ter no mínimo 5 caracteres.',
                    'max_length' => 'O campo endereço deve ter no máximo 255 caracteres.',
                ]
            ],
        ];

        // Define as regras de validação e executa a validação
        $this->validation->setRules($validationRules);

        return $this->validation->run($data);
    }

    private function respostaValida(string $mensagem, array $dados = []): array
    {
        return [
            'status' => true,
            'message' => $mensagem,
            'data' => $dados,
            'error' => false,
            'http_code' => ResponseInterface::HTTP_OK
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

    private function respostaErro(string $mensagem, string $error = ''): array
    {
        return [
            'status' => false,
            'message' => $mensagem,
            'data' => [],
            'error' => $error,
            'http_code' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR
        ];
    }
}
