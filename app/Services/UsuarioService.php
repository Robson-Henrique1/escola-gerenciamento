<?php

namespace App\Services;

use App\Models\UsuarioModel;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class UsuarioService
{
    protected $modelUsuario;
    protected $validation;

    public function __construct()
    {
        $this->modelUsuario = new UsuarioModel();
        $this->validation = Services::validation(); // Obter a instância de validação
    }

    public function logar(string $cpf, string $senha): array
    {
        if (!$this->dadosSaoValidos($cpf, $senha)) {
            return $this->respostaInvalida('Dados incompletos: CPF e senha são obrigatórios.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (!$this->cpfEValido($cpf)) {
            return $this->respostaInvalida('CPF inválido. O CPF deve conter exatamente 11 dígitos.', ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        return $this->autenticarUsuario($cpf, $senha);
    }

    public function registrar(array $data): array
    {
       
        if (!$this->validarDados($data)) {
            
            return $this->respostaInvalida($this->validation->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
 
        
        return $this->criarUsuario($data);
    }


    private function autenticarUsuario(string $cpf, string $senha): array
    {
        try {
            $usuario = $this->modelUsuario->validarUsuario($cpf, $senha);

            if ($usuario) {
                $token = $this->gerarJWT($usuario);
                return $this->respostaValida('Login bem-sucedido', ['token' => $token,'nome' => $usuario['nome']], ResponseInterface::HTTP_OK);
            }

            return $this->respostaInvalida('CPF ou senha incorretos.', ResponseInterface::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar logar: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar fazer login. Tente novamente mais tarde.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function criarUsuario(array $data): array
    {
        try {
            $inseriId = $this->modelUsuario->inseriUsuario(
                $data['nome'],
                $data['cpf'],
                $data['senha'],
                $data['data_nascimento']
            );

            if ($inseriId) {
                return $this->respostaCriada('Usuário cadastrado com sucesso', ['id' => $inseriId], ResponseInterface::HTTP_CREATED);
            }

            return $this->respostaErro('Erro ao cadastrar o usuário.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            log_message('error', 'Exception ao tentar inserir usuário: ' . $e->getMessage());
            return $this->respostaErro('Erro ao tentar cadastrar o usuário.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    private function dadosSaoValidos(string $cpf, string $senha): bool
    {
        return !empty($cpf) && !empty($senha);
    }

    private function cpfEValido(string $cpf): bool
    {
        return preg_match('/^\d{11}$/', $cpf);
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
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'O campo senha é obrigatório.',
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
    
        // Define as regras de validação e executa a validação
        $this->validation->setRules($validationRules);
    
        // Executa a validação com os dados fornecidos
        return $this->validation->run($data);
    }
    

    private function gerarJWT(array $user): string
    {
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user['id'],
            'perfil' => $user['perfil_id']
        ];

        return JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
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

    private function respostaErro(string $mensagem, int $httpCode = ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, string $error = ''): array
    {
        return [
            'status' => false,
            'message' => $mensagem,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
}
