<?php

namespace Entity;

/**
 * Description of BibliografiaTest
 */
class HashTest extends \PHPUnit_Framework_TestCase
{

    public function test_metodo_get_label()
    {
        $hash = new Hash();
        $hash->setValue('aaa');

        $this->assertEquals('aaa', $hash->getLabel());
    }

    public function test_metodo_to_array()
    {
        $hash = new Hash();
        $hash->setId(1);
        $hash->setValue('foobar');

        $arr = array(
            'id' => 1,
            'value' => 'foobar',
        );

        $this->assertEquals($arr, $hash->toArray());
    }

}
