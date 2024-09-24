<?php

namespace Portal\Controller;

use Helpers\SolrPagination;
use Helpers\SolrQuery;
use Helpers\SearchView;

/**
 * Busca
 *
 */
class BuscaController extends PortalController
{

    protected $defaultAction = 'lista';
    protected $solrClient;

    public function __construct($tpl, $session)
    {
        parent::__construct($tpl, $session);
        $solrConfig = require BASE_PATH . 'config/solrConfig.php';
        $this->solrClient = new \Solarium\Client($solrConfig);
    }

    /**
     *
     * @param string $searchQuery
     * @param string $dateQuery
     * @param string $contentTypeQuery
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function querySolr($searchQuery, $dateQuery = null, $contentTypeQuery = null)
    {
        // ## CLIENTE ##
        $query = $this->solrClient->createSelect();
        $query->setQuery($searchQuery);

        // ## FILTROS PADRÃO ##
        // Filtro por data de despublicação
        // (unpublish_date >= NOW ou unpublish_date == NULL)
        $query->createFilterQuery('unpublish_date')
            ->setQuery('-(-unpublish_date:[ NOW TO * ] AND unpublish_date:[ * TO * ])');

        // Filtro por data de publicação
        // (publish_date <= NOW ou publish_date == NULL)
        $query->createFilterQuery('publish_date')
            ->setQuery('-(-publish_date:[ * TO NOW ] AND publish:[ * TO * ])');

        // Filtro pela flag "publicado"
        // (publicado == 1 ou publicado == NULL)
        $query->createFilterQuery('is_publish_true_or_null')
            ->setQuery('-(-publish:1 AND publish:[ * TO * ])');

        // ## FILTROS POR ENTRADA DO USUÁRIO ##
        // Filtro por intervalo de data selecionado pelo usuário
        if ($dateQuery) {
            $query->createFilterQuery('user_input_date')
                ->setQuery($dateQuery);
        }

        // Filtra por tipo de conteúdo
        if ($contentTypeQuery) {
            $query->createFilterQuery('user_input_content_type')
                ->setQuery($contentTypeQuery);
        }

        // ## OPÇÕES DO QUERY PARSER ##
        $edismax = $query->getDisMax();
        // Define campos pesquisáveis e "boost"
        $edismax->setQueryFields('tags^3 title^2 description author');
        // Query alternativa, caso usuário não informe nada
        $edismax->setQueryAlternative('*');
        // Caso encontre exatamente a frase digitada pelo usuário faz "boost" nos campos
        $edismax->setPhraseFields('tags^10 title^10 description^10');
        // Define o mínimo de termos que devem combinar com a busca
        $edismax->setMinimumMatch('75%');

        // ## DESTACA AS PALAVRAS-CHAVE ##
        $highlight = $query->getHighlighting();
        $highlight->setFields('description');
        $highlight->setSimplePrefix('<strong>');
        $highlight->setSimplePostfix('</strong>');
        $highlight->setFragSize(300);
        $highlight->setAlternateField('description');

        // ## ATIVA O DEBUG ##
        $query->getDebug();

        return $query;
    }

    /**
     * Retorna a lista de conteúdos pesquisáveis na busca.
     *
     * @return array
     */
    protected function getContentTypes()
    {
        return array(
            new \Entity\Type('', 'Todos os conteúdos'),
            new \Entity\Type(
                'Entity\\\Ata',
                'Atas'
            ),
            new \Entity\Type(
                'Entity\\\Agenda',
                'Agenda'
            ),
            new \Entity\Type(
                'Entity\\\Bibliografia',
                'Bibliografia Geral do Patrimônio'
            ),
            new \Entity\Type(
                'Entity\\\Biblioteca',
                'Bibliotecas do IPHAN'
            ),
            new \Entity\Type(
                'Entity\\\DicionarioPatrimonioCultural',
                'Dicionário do Patrimônio Cultural'
            ),
            new \Entity\Type(
                'Entity\\\Edital',
                'Editais'
            ),
            new \Entity\Type(
                'Entity\\\Fototeca',
                'Fototecas'
            ),
            new \Entity\Type(
                'Entity\\\Galeria',
                'Galerias'
            ),
            new \Entity\Type(
                'Entity\\\Legislacao',
                'Legislação'
            ),
            new \Entity\Type(
                'Entity\\\LicitacaoConvenioContrato',
                'Licitações, Convênios e Contratos'
            ),
            new \Entity\Type(
                'Entity\\\Noticia',
                'Notícias'
            ),
            new \Entity\Type(
                'Entity\\\Pergunta',
                'Perguntas Frequentes'
            ),
            new \Entity\Type(
                'Entity\\\Publicacao',
                'Publicações'
            ),
            new \Entity\Type(
                'Entity\\\Video',
                'Vídeos'
            ),
        );
    }

    /**
     * Retorna os resultados da busca.
     *
     * @param array $params
     * @return object
     */
    protected function getResults($params)
    {
        $query = new SolrQuery();
        $dateQuery = $query->mountDateClause(
            $params->get('date_from'),
            $params->get('date_to')
        );
        $contentTypeQuery = $query->mountContentTypeClause(
            $params->get('content_type')
        );

        return $this->querySolr(
            $params->get('search_query'),
            $dateQuery,
            $contentTypeQuery
        );
    }

    /**
     * Listagem de resultados da busca.
     *
     * @return string
     */
    public function lista()
    {
        $params = $this->getParam();
        $results = $this->getResults($params);
        $pagination = new SolrPagination($this->solrClient, $results);
        $contentTypes = $this->getContentTypes();

        $this->getTpl()->setTitle('Busca');
        $viewData = array(
            'content_types' => $contentTypes,
            'helpers' => new SearchView(),
            'search_query' => $params->get('search_query'),
            'content_type' => $params->get('content_type'),
            'date_from' => $params->get('date_from'),
            'date_to' => $params->get('date_to'),
        );

        if ($params->get('do_search') == 'y') {
            try {
                $viewData['pagination'] = $pagination->render();
                $viewData['results'] = $pagination->results();
            } catch (\Solarium\Exception\HttpException $e) {
                // Captura a exceção caso o Apache Solr esteja indisponível
                // (offline ou aplicação mal configurada).
                $viewData['error'] = 'Não foi possível realizar a busca. Ocorreu um erro.';
            }
        } else {
            $viewData['pagination'] = null;
            $viewData['results'] = null;
        }
        $this->getTpl()->renderView($viewData);

        return $this->getTpl()->output();
    }
}
