<?php

namespace Entity;

/**
 * Description of ComentarioNoticiaTest
 *
 * @author Jointi
 */
class ComentarioNoticiaTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $noticia = new \Entity\Noticia();
        $noticia->setTitulo('titulo');

        $entity = new ComentarioNoticia();
        $entity->setNoticia($noticia);
        $entity->setComentario('teste');

        $this->assertEquals($entity->getComentario()
                . ' em <a style="color: blue;" href="noticia/form/'
                . $entity->getNoticia()->getId()
                . '">&laquo;&nbsp;'
                . mb_strtoupper($entity->getNoticia()->getTitulo(), 'UTF-8')
                . '&nbsp;&raquo;</a>', $entity->getLabel());
    }

    public function test_metodo_toArray()
    {
        $entity = new ComentarioNoticia();
        $entity->setId(1);
        $entity->setDataCadastro(new \DateTime('now'));
        $entity->setDataInicial(new \DateTime('now'));
        $entity->setDataFinal(new \DateTime('now'));
        $entity->setAutor('autor');
        $entity->setEmail('teste@teste.com');
        $entity->setPublicado(1);
        $entity->setNoticia(new \Entity\Noticia());
        
        $arr = array(
            'id' => 1,
            'dataCadastro' => new \DateTime('now'),
            'dataInicial' => new \DateTime('now'),
            'dataFinal' => new \DateTime('now'),
            'autor' => 'autor',
            'email' => 'teste@teste.com',
            'publicado' => 1,
            'noticia' => new \Entity\Noticia()
        );
        
        $toArray = $entity->toArray();
        $this->assertEquals($toArray, $arr);
    }

}
