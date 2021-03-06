<?php


/**
 *
 * @category linked-swissbib
 * @package  /
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */


namespace LinkedSwissbib\Module\Config;


$config = [
    'router' => [
        'routes' => []
    ],
    'controllers' => [
        'invokables' => [
            'exploration'   => 'LinkedSwissbib\Controller\ElasticsearchController',
            'elasticsearch' => 'LinkedSwissbib\Controller\ElasticsearchController',
            'inference'     => 'LinkedSwissbib\Controller\SparqlController',
            'ajax'          => 'LinkedSwissbib\Controller\AjaxController',
            'search'        => 'LinkedSwissbib\Controller\SearchController'


        ]
    ],
    'service_manager' => [
        'factories' => [
            'LinkedSwissbib\SearchOptionsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchOptionsPluginManager',
            'LinkedSwissbib\SearchParamsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchParamsPluginManager',
            'LinkedSwissbib\SearchResultsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchResultsPluginManager',
            'LinkedSwissbib\RecordDriverPluginManager' => 'LinkedSwissbib\Service\Factory::getRecordDriverPluginManager',
            'VuFind\SearchOptionsPluginManager'        => 'LinkedSwissbib\Service\Factory::getSearchOptionsPluginManager',
            'VuFind\SearchParamsPluginManager'         => 'LinkedSwissbib\Service\Factory::getSearchParamsPluginManager',
            'VuFind\SearchResultsPluginManager'        => 'LinkedSwissbib\Service\Factory::getSearchResultsPluginManager',
            'Swissbib\ExtendedSolrFactoryHelper'       => 'LinkedSwissbib\Search\Helper\Factory::getExtendedSolrFactoryHelper',



        ]

    ],
    'vufind' => [
        'plugin_managers' => [
            'search_backend' => [
                'factories' => [
                    'ElasticSearch' => 'LinkedSwissbib\Search\Factory\ElasticSearchBackendFactory',
                ]
            ],
            'autocomplete' => [
                'factories' => [
                    'esac' => 'LinkedSwissbib\VuFind\Autocomplete\Factory::getElasticSearch',
                    'solr' =>  'LinkedSwissbib\VuFind\Autocomplete\Factory::getSolr',
                ],
            ],
            'recorddriver' => [
                'abstract_factories' => ['LinkedSwissbib\RecordDriver\PluginFactory'],
                'factories' => [
                    'elasticsearchRecordDriver' => 'LinkedSwissbib\RecordDriver\Factory::getElasticSearchRdfRecordDriver',

                ]
            ]

        ]
    ],
    'swissbib' => [

        //todo: do we need a single linkedSwissbib key for the plugin managers which should be merged with the swissbib key and in the
        // with the vufind keys
        //vufind_search_options seems to be deprecated now search_options
        //look it up (we need it for saved searches
        'plugin_managers' => [
            'search_options' => [
                'abstract_factories' => array('LinkedSwissbib\Search\Options\PluginFactory'),
            ],
            'search_params' => [
                'abstract_factories' => array('LinkedSwissbib\Search\Params\PluginFactory'),
            ],
            'search_results' => [
                'abstract_factories' => array('LinkedSwissbib\Search\Results\PluginFactory'),
            ],

            'search_backend' => [
                'factories' => [
                    'ElasticSearch' => 'LinkedSwissbib\Search\Factory\ElasticSearchBackendFactory',
                ]
            ],
            'autocomplete' => [
                'factories' => [
                    'esac' => 'LinkedSwissbib\VuFind\Autocomplete\Factory::getElasticSearch',
                    'solr' =>  'LinkedSwissbib\VuFind\Autocomplete\Factory::getSolr',
                ],
            ],
            'recorddriver' => [
                'abstract_factories' => ['LinkedSwissbib\RecordDriver\PluginFactory'],
                'factories' => [
                    'elasticsearchRecordDriver' => 'LinkedSwissbib\RecordDriver\Factory::getElasticSearchRdfRecordDriver',

                ]
            ]


        ]
    ]


];


// Define static routes -- Controller/Action strings
$staticRoutes = [
    'Elasticsearch/Results',
    'Sparql/Results',
    'Exploration/AuthorDetails',
    'Exploration/SubjectDetails',
    'Ajax/Json',
    'Elasticsearch/Home'
];

$routeGenerator = new \VuFind\Route\RouteGenerator();
$routeGenerator->addStaticRoutes($config, $staticRoutes);


return $config;

