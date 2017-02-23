<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Elasticsearch_Options
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Search\Elasticsearch;

use VuFind\Search\Base\Options as BaseOptions;


class Options  extends BaseOptions
{

    protected $autocompleteEnabled = true;

    /**
     * Return the route name for the search results action.
     *
     * @return string
     */
    public function getSearchAction()
    {
        return 'elasticsearch-results';
    }




}