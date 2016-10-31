<?php

/**
 *
 * @category linked-swissbib
 * @package  Controller_Elasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Controller;
use VuFind\Controller\AbstractSearch;

class ElasticsearchController extends AbstractSearch
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchClassId = 'Elasticsearch';
        parent::__construct();
    }


    public function searchAction ()
    {
        return parent::resultsAction();
    }

    public function resultsAction()
    {
        $query = $this->getRequest()->getQuery();
        $query->set('type','Person');
        return parent::resultsAction();
    }

    public function authorAction ()
    {
        return parent::resultsAction();

    }

    public function AuthorDetailsAction ()
    {
        return parent::resultsAction();

    }

    public function SubjectDetailsAction ()
    {
        return parent::resultsAction();

    }

    public function homeAction() {
        return parent::resultsAction();
    }


    /**
     * Convenience method for accessing results
     *
     * @return \LinkedSwissbib\Search\Results\PluginManager
     */
    protected function getResultsManager()
    {
        return $this->getServiceLocator()->get('LinkedSwissbib\SearchResultsPluginManager');
    }


}