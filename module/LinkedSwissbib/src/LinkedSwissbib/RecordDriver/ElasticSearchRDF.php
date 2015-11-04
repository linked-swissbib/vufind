<?php
/**
 *
 * @category linked-swissbib
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\RecordDriver;
use VuFind\RecordDriver\AbstractBase;

class ElasticSearchRDF extends AbstractBase {

    protected $propertiesToNameSpaces = [
        'rdf:type'  => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        'dc11:format'   => 'http://purl.org/dc/elements/1.1/format',
        'dc:hasPart'    => 'http://purl.org/dc/terms/hasPart',
        'dc:issued' =>  'http://purl.org/dc/terms/issued',
        'dc:language'   =>  'http://purl.org/dc/terms/language',
        'dc:title'  =>  'http://purl.org/dc/terms/title',
        'bibo:isbn10'   =>  'http://purl.org/ontology/bibo/isbn10',
        'bibo:isbn13'   =>  'http://purl.org/ontology/bibo/isbn13',
        'ns0:contentType'   =>  'http://rdaregistry.info/Elements/u/contentType',
        'ns0:mediaType' =>  'http://rdaregistry.info/Elements/u/mediaType',
        'ns0:noteOnResource'    =>  'http://rdaregistry.info/Elements/u/noteOnResource',
        'ns0:placeOfPublication'    =>  'http://rdaregistry.info/Elements/u/placeOfPublication',
        'ns0:publicationStatement'  =>  'http://rdaregistry.info/Elements/u/publicationStatement',
        'rdfs:isDefinedBy'  =>  'http://www.w3.org/2000/01/rdf-schema#isDefinedBy'

    ];


    /**
     * Get text that can be displayed to represent this record in breadcrumbs.
     *
     * @return string Breadcrumb text to represent this record.
     */
    public function getBreadcrumb()
    {
        throw new \Exception(__FUNCTION__ . ' currently not supported');
    }

    /**
     * Return the unique identifier of this record for retrieving additional
     * information (like tags and user comments) from the external MySQL database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        return $this->fields['_source']['@id'];
    }


    public function getRdf($rdfType = 'turtle')
    {

        $rdfGraph = new \EasyRdf_Graph();
        try {
            $rdfGraph->parse(json_encode($this->fields['_source']), 'jsonld');

            return $rdfGraph->serialise($rdfType);
        } catch (\Exception $ex) {
            return "";
        }

    }


    public function getRdfType()
    {
        if (isset($this->fields['_source']['@type']))
        {
            return is_array($this->fields['_source']['@type']) ? $this->fields['_source']['@type'] :
                [$this->fields['_source']['@type']];
        } else {
            return [];
        }


    }

    public function getLanguage()
    {
        if (isset($this->fields['_source']['dct:language']))
        {
            return is_array($this->fields['_source']['dct:language']) ? array_values($this->fields['_source']['dct:language']) :
                [$this->fields['_source']['dct:language']];
        } else {
            return [];
        }

    }

    public function getFirstName()
    {
        return $this->fields['_source']['foaf:firstName'];
    }

    public function getLastName()
    {
        return $this->fields['_source']['foaf:lastName'];
    }

}