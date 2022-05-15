<?php
namespace App\Models;

use CodeIgniter\Model;

class Pins extends Model
{
    protected $table = 'pin';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['pin','worth','owner','viewed','used'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
