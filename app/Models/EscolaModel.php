<?php

namespace App\Models;

use CodeIgniter\Model;

class EscolaModel extends Model
{
    protected $table = 'escolas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'endereco'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[100]',
        'endereco' => 'required|min_length[5]|max_length[255]'
    ];


    public function inseriEscola($nome, $endereco)
    {
        // Insere o usuário no banco de dados usando o Query Builder
        $this->db->table($this->table)->insert([
            'nome'             => $nome,
            'endereco'         => $endereco,
        ]);

        $inseritId = $this->db->insertID();

        return $inseritId; // Retorna o ID do registro inserido
    }

    public function pegarEscolaPorId($id)
    {
        // Faz a consulta e seleciona todos os campos
        return $this->select('id, nome, endereco')
                    ->where('id', $id)
                    ->first();
    }


    public function deletarEscola($id)
    {
        // Verifica se o aluno existe
        $aluno = $this->where(['id' => $id])->first();

        if ($aluno) {
            // Deleta o aluno
            return $this->delete($id);
        }

        return false; // Retorna false se o aluno não existir
    }
    public function atualizarEscola(int $id, array $data): bool
    {
        // Usa o Query Builder para fazer o update
        $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data);

        // Verifica se a atualização foi bem-sucedida
        return $this->db->affectedRows() > 0;
    }
}
