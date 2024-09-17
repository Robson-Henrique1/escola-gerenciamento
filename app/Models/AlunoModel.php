<?php

namespace App\Models;

use CodeIgniter\Model;

class AlunoModel extends Model
{
    protected $table = 'alunos'; // Nome da tabela
    protected $primaryKey = 'id'; // Chave primária
    protected $allowedFields = ['nome', 'cpf', 'data_nascimento', 'professor_id']; // Campos permitidos
    protected $useTimestamps = true;

    /**
     * Função personalizada para listar todos os alunos
     * 
     * @return array Lista de alunos
     */
    public function pegarAlunos()
    {
        // Faz a consulta e seleciona todos os campos
        return $this->select('id, nome, cpf, data_nascimento, professor_id')
                    ->findAll();
    }

    /**
     * Função personalizada para obter um aluno por ID
     * 
     * @param int $id ID do aluno
     * @return array|object Aluno encontrado
     */
    public function pegarAlunoPorId($id)
    {
        // Faz a consulta e seleciona todos os campos
        return $this->select('id, nome, cpf, data_nascimento, professor_id')
                    ->where('id', $id)
                    ->first();
    }

    /**
     * Função personalizada para criar um novo aluno
     * 
     * @param array $data Dados do aluno
     * @return bool|int Retorna o ID do aluno inserido ou false em caso de erro
     */
    public function inseriAluno($nome, $cpf, $dataNascimento, $professorId)
    {
        // Insere o aluno no banco de dados usando o Query Builder
        $this->db->table($this->table)->insert([
            'nome'             => $nome,
            'cpf'              => $cpf,
            'data_nascimento'  => $dataNascimento,
            'professor_id'     => $professorId
        ]);

        $inseriId = $this->db->insertID();

        return $inseriId; // Retorna o ID do aluno inserido ou false em caso de falha
    }

    public function deletarAluno($id)
    {
        // Verifica se o aluno existe
        $aluno = $this->where(['id' => $id])->first();

        if ($aluno) {
            // Deleta o aluno
            return $this->delete($id);
        }

        return false; // Retorna false se o aluno não existir
    }
    public function atualizarAluno(int $id, array $data): bool
    {
        // Usa o Query Builder para fazer o update
        $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data);

        // Verifica se a atualização foi bem-sucedida
        return $this->db->affectedRows() > 0;
    }
}
