<?php

namespace Entity\Repository;

use Helpers\Session;

class BannerGeralCategoriaRepository extends BaseRepository
{

    protected $entity = 'Entity\BannerGeralCategoria';
    protected $search_column = 'nome';
    protected $user;

    public function findAllBySite($user)
    {
        if (!$user['sede'] || empty($user['sede']) || !in_array(1, $user['subsites'])) {
            return $this->findBy(array('id' => array(7, 6)), array('nome' => 'ASC'));
        }
        else {
            return $this->findBy(array(), array('nome' => 'ASC'));
        }
    }
    
    public function findAllById($array)
    {
        
        return $this->findBy(array('id' => $array), array('nome' => 'ASC'));
        
    }
    
    

}
