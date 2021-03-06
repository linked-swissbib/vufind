<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Backend\Elasticsearch;


use LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\MultisearchQuery;
use LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Query;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Query\Query as VuFindQuery;


class ESQueryBuilder implements ESQueryBuilderInterface
{

    /**
     * @var ESParamBag
     */
    protected $params;

    /**
     * Search specs for exact searches.
     *
     * @var array
     */
    protected $exactSpecs = [];

    /**
     * Search specs.
     *
     * @var array
     */
    protected $specs = [];

    /**
     * Indexes the search is executed on
     * @var array
     */
    //todo: make it configurable
    protected $searchIndexes = ['lsb'];




    /**
     * Constructor.
     * @param array  $specs                Search handler specifications
     * @param string $defaultDismaxHandler Default dismax handler (if no
     * DismaxHandler set in specs).
     *
     */
    public function __construct(array $specs = [],
                                $defaultDismaxHandler = 'dismax'
    ) {
        $this->defaultDismaxHandler = $defaultDismaxHandler;
        $this->setSpecs($specs);
    }




    /**
     * Build build a query for the target based on VuFind query object.
     * @param AbstractQuery $query Query object
     * @return ParamBag
     */
    public function build(AbstractQuery $vuFindQuery)
    {
        /** @var SearchHandler $searchHandlerType */
        if ($vuFindQuery instanceof VuFindQuery) {
            $searchHandlerType = $this->getSearchHandler($vuFindQuery->getHandler());
        } else {
            $searchHandlerType = $this->getSearchHandler('allfields');
        }

        // Queries for AJAX controller
        // Searches in index lsb; @id in type person, organisation and bibliographic resource, as well as dbp:movement and dbp:genre, dct:contributor, dct:subject
        if ($vuFindQuery->getHandler() == 'AuthorByIdMulti') {
            $querystring = $vuFindQuery->getString();
            $uris = explode(',',$querystring);
            //$getParams['body'] = '{"index":"lsb","type":"person"}'."\n".'{"query":{"filtered":{"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}}}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"filtered":{"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"filtered":{"filter":{"in":{"dct:contributor":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"filtered":{"filter":{"in":{"dct:subject":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n";
            //$getParams['body'] = '{"index":"lsb","type":"person"}'."\n".'{"query":"bool":{"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}}}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"filter":{"in":{"dct:contributor":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"filter":{"in":{"dct:subject":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n";
            $getParams['body'] = '{"index":"lsb","type":"person"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}}}}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"dct:contributor":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"bibliographicResource"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"dct:subject":["' . implode('","', $uris) . '"]}}}},"size":100}'."\n".'{"index":"lsb","type":"person"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"dbp:genre":["' . implode('","', $uris) . '"]}}}}}'."\n".'{"index":"lsb","type":"person"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"dbp:movement":["' . implode('","', $uris) . '"]}}}}}'."\n".'{"index":"lsb","type":"organisation"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"@id":["' . implode('","', $uris) . '"]}}}}}'."\n";
            $getParams['type'] = array("person"); //TODO: Find out whether this line is necessary
            $getParams['index'] = $searchHandlerType->getIndices();

            return $getParams;
        }

        // Queries for AJAX controller
        // Searches in index gnd; _id in type DEFAULT
        if ($vuFindQuery->getHandler() == 'SubjectByIdMulti') {
            $querystring = $vuFindQuery->getString();
            $uris = explode(',',$querystring);
            $getParams['body'] = '{"index":"gnd","type":"DEFAULT"}'."\n".'{"query":{"bool":{"must":[{"match_all":{}}],"filter":{"in":{"_id":["' . implode('","', $uris) . '"]}}}}}'."\n";
            //$getParams['body'] = '{"index":"gnd","type":"DEFAULT"}'."\n".'{"query":"bool":{"filter":{"in":{"_id":["' . implode('","', $uris) . '"]}}}}'."\n";
            //$getParams['body'] = '{"index":"gnd","type":"DEFAULT"}'."\n".'{"query":{"filtered":{"filter":{"in":{"_id":["' . implode('","', $uris) . '"]}}}}}'."\n";
            //$getParams['body'] = '{"query":{"filtered":{"filter":{"in":{"_id":["' . implode('","', $uris) . '"]}}}}}'."\n";
            $getParams['type'] = array("DEFAULT"); //TODO: Find out whether this line is necessary
            $getParams['index'] = array("gnd");

            return $getParams;
        }

        $esQuery = new MultisearchQuery($vuFindQuery,$searchHandlerType->getSpec(), $this);
        $searchBody =  $esQuery->build();

        //Quick fix: Enable return of correct number of items (results)
        //strip last element from search expression
        $searchBody=substr($searchBody,0,-2); //remove "}\n" from search expression
        //Add size to search body
        $size=$this->params->get('size')[0];
        if (isset($size) && is_numeric($size))
        {
            //add number of results to search expression
            $searchBody = $searchBody . ',"size": ' . $size;
        }
        //Add offset to search body
        $from=$this->params->get('from')[0];
        if (isset($size) && is_numeric($size))
        {
            //add start index to search expression
            $searchBody = $searchBody . ',"from": ' . $from;
        }
        //finalise search expression
        $searchBody = $searchBody . "}\n";



        $getParams['body'] = $searchBody;
        $getParams['type'] = array("person", "DEFAULT");
        $getParams['index'] = array("lsb", "gnd");

        return $getParams;
    }

    public function setParams(ESParamBag $paramsBag)
    {
        $this->params = $paramsBag;
    }

    /**
     * Set query builder search specs.
     * @param array $specs Search specs
     * @return void
     */
    public function setSpecs(array $specs)
    {
        foreach ($specs as $handler => $spec) {
            if (isset($spec['ExactSettings'])) {
                $this->exactSpecs[strtolower($handler)] = new SearchHandler(
                    $spec['ExactSettings'], $this->defaultDismaxHandler
                );
                unset($spec['ExactSettings']);
            }
            $this->specs[strtolower($handler)]
                = new SearchHandler($spec, $this->defaultDismaxHandler);
        }
    }


    /**
     * @return SearchHandler
     */
    public function getSearchHandler($queryHandlerString)
    {
        return  array_key_exists(strtolower($queryHandlerString),$this->specs) ? $this->specs[strtolower($queryHandlerString)] :
            $this->specs['allfields'];
    }


}