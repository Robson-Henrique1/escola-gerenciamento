<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    protected $usuarioService;
    protected $format = 'json';

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        
    }

    public function logar()
    {
        $data = $this->request->getJSON(true);
        $response = $this->usuarioService->logar($data['cpf'], $data['senha']);

        return $this->respond($this->formatarResposta($response), $response['http_code']);
    }

    public function registrar()
    {
        $data = $this->request->getJSON(true);
        $response = $this->usuarioService->registrar($data);

        return $this->respond($this->formatarResposta($response), $response['http_code']);
    }


    private function formatarResposta(array $response): array
    {
        return [
            'message' => $response['message'],
            'data' => $response['data'] ?? null,
            'error' => $response['error'] ?? null
        ];
    }
}
