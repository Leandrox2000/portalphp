<?php

namespace Html;

use \Template\TemplateAmanda;

class Table 
{

    	private $id;
	private $numColumns;
        
	private $row            = array();
	private $currCol        = 0;
        
	private $idHidden       = array();
	private $valueHidden    = array();

        private $body           = array();
        private $head           = "";
        private $footer         = "";

        /**
         * 
         * @param string $id
         * @param string $numColumns
         */
	public function __construct($id="", $numColumns=0) 
        {
		$this->id = $id;
		$this->numColumns = $numColumns;
	}

        /**
         * O Id da Coluna no HTML
         * 
         * @return string
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * 
         * 
         * @return int
         */
        public function getNumColumns()
        {
            return $this->numColumns;
        }
        
        /**
         * 
         * @param string $id
         * @return Table $this
         */
        public function setId($id)
        {
            $this->id = $id;
            
            return $this;
        }

        /**
         * 
         * @param int $numColumns
         * @return Table $this
         */
        public function setNumColumns($numColumns)
        {
            $this->numColumns = $numColumns;
            
            return $this;
        }

        /**
         * 
         * @param string $id
         * @param string $value
         * @return Table $this
         */
	public function addHidden($id, $value) 
        {
		$this->idHidden[] = $id;
		$this->valueHidden[] = $value;
                
                return $this;
	}
	
        /**
         * Privado / Monta uma lina na Tabela
         * 
         * @param array $row
         * @param head/body/footer $type
         * @return Table $this
         */
	private function addRow($row, $type) 
        {
                if ($type=='head') {
                    $this->head = $row;
                } elseif($type=='body') {
                    $this->body[] = $row;
                } elseif($type=='footer') {
                    $this->footer = $row;
                }
            
		$this->row      = array();
		$this->currCol  = 0;
                
                return $this;
	}

        /**
         * Adiciona uma coluna no Body da Tabela
         * 
         * @param string $data Conteúdo da Coluna
         * @param string $class Define o Alinhamento center/right- Defalut left
         * @return Table $this
         */
	public function addData($data = "&nbsp", $class = "") 
        {
                $this->currCol++;
                
		$this->row[$this->currCol]['data']  = $data;
		$this->row[$this->currCol]['class'] = $class;
		
		if ($this->numColumns == $this->currCol) {
                    $this->addRow($this->row, 'body');
                }
                
                return $this;
	}

        /**
         * 
         * @param string $title
         * @param string $width
         * @param string $class
         * @return Table $this
         */
	public function addColumnHeader($title = "&nbsp;", $width = "1", $class = "")
        {
                $this->currCol++;
                
		$this->row[$this->currCol]['data']  = $title;
		$this->row[$this->currCol]['width'] = $width;
		$this->row[$this->currCol]['class'] = $class;
		
		if ($this->numColumns == $this->currCol) {
                    $this->addRow($this->row, 'head');
                }
                
                return $this;
	}
        
        /**
         * 
         * @param string $title
         * @param string $width
         * @param string $class
         */
	public function addColumnFooter($title = "&nbsp;", $class = "")
        {
                $this->currCol++;
                
		$this->row[$this->currCol]['data']  = $title;
		$this->row[$this->currCol]['class'] = $class;
		
		if ($this->numColumns == $this->currCol) {
                    $this->addRow($this->row, 'footer');
                }
                
                return $this;
	}

        /**
         * Retorna a Tabela REndeizada em HTML
         * 
         * @return HTML
         */
	public function getHTML() 
        {
            $tpl    = new TemplateAmanda();
            $loader = $tpl->getLoader();
            $twig   = new \Twig_Environment($loader);
            $twig->addExtension(new \Twig_Extension_Escaper('html'));

            return $twig->render("table.html.twig", array(
                                                'id'        =>  $this->id,
                                                'head'      =>  $this->head,
                                                'body'      =>  $this->body,
                                                'footer'    =>  $this->footer,
                                            )
                                );
	}
        
        /**
         * Imprime a table do Objeto (converte para String)
         * 
         * @return HTML
         */
        public function __toString()
        {
            return $this->getHTML();
        }

}
