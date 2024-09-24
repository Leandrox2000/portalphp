<?php

namespace Template;

/**
 * Deve ser implementada para adicionar  u mTemplate
 *
 * @author Luciano
 */
interface TemplateInterface
{

    public function loadConfig();

    public function setHeader();

    public function setTop();

    public function setMenu();
    
    public function setRedesSociais();
    
    public function setContent($content);

    public function setFooter();

    public function setView($view);

    public function addJS($script);

    public function addCSS($css);

    public function viewExists($view);

    public function jsExists($script);

    public function cssExists($css);

    public function output();

    public function renderView(array $content);

    public function getHtml();

    public function __toString();

    public function addGlobal($name, $value);

}
