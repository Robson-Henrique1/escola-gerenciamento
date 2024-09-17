<?php

namespace App\Controllers;

use App\Services\EscolaService;
use CodeIgniter\RESTful\ResourceController;

class EscolaController extends ResourceController
{
    protected $escolaService;
    protected $format = 'json';
    
    public function __construct()
    {
        $this->escolaService = new EscolaService();
    }

    public function index()
    {
        $response = $this->escolaService->obterTodasEscolas();
        return $this->respond($this->formatarResposta($response), $response['http_code']);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $response = $this->escolaService->cadastrarEscola($data);
        return $this->respond($this->formatarResposta($response), $response['http_code']);
    }

    public function delete($id = null)
    {
        $response = $this->escolaService->deletarEscola($id);
        
        return $this->respond($response, $response['http_code']);
    }

    public function update($id = null)
    {
        
        // Converte o ID para inteiro e obtem os dados da requisição
        $id = (int)$id;
        $data = $this->request->getJSON(true);

        // Chama o método atualizarAluno do service

        $response = $this->escolaService->atualizarEscola($id, $data);
        
        return $this->respond($response, $response['http_code']);
    }

    private function formatarResposta(array $response): array
    {
        return [
            'status' => $response['status'],
            'message' => $response['message'],
            'data' => $response['data'] ?? null,
            'error' => $response['error'] ?? null
        ];
    }
}
