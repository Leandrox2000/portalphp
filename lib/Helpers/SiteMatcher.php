<?php

namespace Helpers;

class SiteMatcher {

    /**
     * Lista de sites.
     *
     * @var array
     */
    private $sites;
    /**
     * Lista de expressões regulares somente do subsite.
     *
     * @var array
     */
    private $sitesRegex;
    /**
     * Lista de expressões regulares do subsite
     * com mais informações após a barra.
     *
     * @var array
     */
    private $sitesRegexExtra;
    /**
     * Site
     *
     * @var \Entity\Site
     */
    private $matchedSite;

    public function __construct($sites)
    {
        $this->sites = $sites;
        $this->sitesRegex = array();
        $this->sitesRegexExtra = array();
        $this->generateRegex();
    }

    /**
     * Gera as expressões regulares utilizadas para testar se é subsite.
     */
    private function generateRegex()
    {
        foreach ($this->sites as $site) {
            $this->sitesRegex[] = '/^' . mb_strtolower($site->getSigla()) . '$/';
            $this->sitesRegexExtra[] = '/^' . mb_strtolower($site->getSigla()) . '\//';
        }
    }

    /**
     * Verifica se é uma URL de subsite ou não.
     *
     * @param string $url
     * @param integer $key
     * @return boolean
     */
    private function matchSite($url, $key)
    {
        return (
            preg_match($this->sitesRegex[$key], $url) ||
            preg_match($this->sitesRegexExtra[$key], $url)
        );
    }

    /**
     * Gera informações para debugar.
     *
     * @param string $url
     * @param integer $key
     * @return string
     */
    private function debug($url, $key)
    {
        $out = "-------------------------\n";
        $out .= "siteRegex: " . $this->sitesRegex[$key] . "\n";
        $out .= "siteRegexExtra: " . $this->sitesRegexExtra[$key] . "\n";
        $out .= "url: " . $url . "\n";

        return $out;
    }

    /**
     * Verifica se é uma uri de subsite.
     *
     * @param string $url
     * @return boolean|\Entity\Site
     */
    public function matchAnySite($url)
    {
        foreach ($this->sites as $key => $site) {
            // echo $this->debug($url, $key);
            if ($this->matchSite($url, $key)) {
                $this->matchedSite = $site;

                return $site;
            }
        }

        return false;
    }

    /**
     * Remove o site da URL para retornar somente a rota
     * relevante ao FrontController.
     *
     * @param string $path
     * @return string
     */
    public function getPathWithoutSite($path)
    {
        // Se não encontrou nenhum site
        if ($this->matchedSite === null) {
            // Retorna o caminho sem nenhuma alteração
            return $path;
        }

        // Pega a sigla
        $sigla = $this->matchedSite->getSigla();
        // Calcula a posição da sigla
        $posicaoSigla = strlen($sigla) + 1;

        // Remove o prefixo e retorna o caminho.
        return substr_replace($path, '', 0, $posicaoSigla);
    }

}
