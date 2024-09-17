<?php

namespace App\Controllers;

use App\Services\ProfessorService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class ProfessorController extends ResourceController
{
    protected $professorService;
    protected $format = 'json'; // Define o formato da resposta como JSON

    public function __construct()
    {
        $this->professorService = new ProfessorService();
    }

    public function index()
    {
        $response = $this->professorService->listarProfessores();
        return $this->respond($response, $response['http_code']);
    }

    public function delete($id = null)
    {
        $response = $this->professorService->deletarProfessor($id);
        
        return $this->respond($response, $response['http_code']);
    }

    public function update($id = null)
    {
        // Converte o ID para inteiro e obtem os dados da requisição
        $id = (int)$id;
        $data = $this->request->getJSON(true);

        // Chama o método atualizarProfessor do service
        $response = $this->professorService->atualizarProfessor($id, $data);

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

        $response = $this->professorService->cadastrarProfessor($data);
        return $this->respond($response, $response['http_code']);
    }
}
