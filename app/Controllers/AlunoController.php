<?php

namespace App\Controllers;

use App\Services\AlunoService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class AlunoController extends ResourceController
{
    protected $alunoService;
    protected $format = 'json'; // Define o formato da resposta como JSON

    public function __construct()
    {
        $this->alunoService = new AlunoService();
    }

    public function index()
    {
        $response = $this->alunoService->listarAlunos();
        return $this->respond($response, $response['http_code']);
    }

    public function delete($id = null)
    {
        $response = $this->alunoService->deletarAluno($id);
        
        return $this->respond($response, $response['http_code']);
    }

    public function update($id = null)
    {
        // Converte o ID para inteiro e obtem os dados da requisição
        $id = (int)$id;
        $data = $this->request->getJSON(true);

        // Chama o método atualizarAluno do service
        $response = $this->alunoService->atualizarAluno($id, $data);

        return $this->respond($response, $response['http_code']);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (is_null($data)) {
            return $this->fail(
                'Dados inválidos ou não fornecidos.',
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
        
        $response = $this->alunoService->criarAluno($data);
        return $this->respond($response, $response['http_code']);
    }
}
