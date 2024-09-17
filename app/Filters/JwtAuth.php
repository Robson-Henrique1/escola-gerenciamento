<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\Key;

class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        
        // Obtém o valor do cabeçalho Authorization
        $authHeader = $request->getHeaderLine('Authorization');

        // Verifica se o cabeçalho Authorization foi fornecido
        if (!$authHeader) {
            return $this->respondUnauthorized('Token JWTte não fornecido.');
        }

        // Remove o prefixo "Bearer " do cabeçalho Authorization
        $token = str_replace('Bearer ', '', $authHeader);
        $secretKey = getenv('JWT_SECRET_KEY');

        try {

            // Decodifica o token JWT
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // Certifique-se de que $decoded é do tipo stdClass
            if (!is_object($decoded)) {
                return $this->respondUnauthorized('Token JWT inválido.');
            }

            // Adiciona os dados decodificados à requisição
            $request->user = $decoded;
        } catch (ExpiredException $e) {
            return $this->respondUnauthorized('Token JWT expirado.');
        } catch (SignatureInvalidException $e) {
            return $this->respondUnauthorized('Assinatura do token JWT inválida.');
        } catch (\Exception $e) {
            return $this->respondUnauthorized('Token JWT inválido.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não faz nada após a requisição
    }

    private function respondUnauthorized($message)
    {
        return service('response')->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, $message);
    }
}
