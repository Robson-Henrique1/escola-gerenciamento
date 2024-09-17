<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'cpf', 'senha', 'data_nascimento'];
    protected $useTimestamps = true;


    public function validarUsuario($cpf, $senha)
    {
        $user = $this->where('cpf', $cpf)->first();
        if ($user && password_verify($senha, $user['senha'])) {
            return $user;
        }
        return false;
    }

    /**
     * Função personalizada para inserir um novo usuário
     * 
     * @param array $data Dados do usuário
     * @return bool|int Retorna o ID do usuário inserido ou false em caso de erro
     */
    public function inseriUsuario($nome, $cpf, $senha, $dataNascimento,$escolaId = null,$perfilId = 1)
    {
        // Hash da senha antes de inserir
        $senhaHashed = password_hash($senha, PASSWORD_BCRYPT);

        // Insere o usuário no banco de dados usando o Query Builder
         $this->db->table($this->table)->insert([
            'nome'             => $nome,
            'cpf'              => $cpf,
            'senha'            => $senhaHashed,
            'data_nascimento'  => $dataNascimento,
            'escola_id'        => $escolaId,
            'perfil_id'        => $perfilId 
        ]);
        $inseriId = $this->db->insertID();

        return $inseriId; // Retorna o ID do usuário inserido ou false em caso de falha
    }

    public function pegarProfessores()
    {
        // Faz a consulta e seleciona todos os campos exceto a senha
        return $this->select('id, nome, cpf, data_nascimento, escola_id')
                    ->where('perfil_id', 2)
                    ->findAll();
    }

    public function pegarProfessorPorId($id)
    {
        // Faz a consulta e seleciona todos os campos
        return $this->select('id, nome, senha, cpf, data_nascimento, escola_id')
                    ->where('id', $id,'perfil_id', 2)
                    ->first();
    }

    public function deletarProfessor($id)
    {
        // Verifica se o professor existe
        $professor = $this->where(['id' => $id, 'perfil_id' => 2])->first();

        if ($professor) {
            // Deleta o professor
            return $this->delete($id);
        }

        return false; // Retorna false se o professor não existir
    }
    public function atualizarProfessor(int $id, array $data): bool
    {

        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_BCRYPT);
        }


        // Usa o Query Builder para fazer o update
        $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data);

        // Verifica se a atualização foi bem-sucedida
        return $this->db->affectedRows() > 0;
    }


}
