<?php

namespace Ssch\TYPO3Rector\Tests\Rector\Extensions\solr\v9\ApacheSolrDocumentToSolariumDocumentRector\Fixture;

use Apache_Solr_Document;

class MySolrIndexer
{
    public function __construct()
    {
    }

    public function index()
    {
        $document = new Apache_Solr_Document();
        $document->setMultiValue('key', 'foo', true);
    }

    public function multiple(array $documents)
    {
        /** @var Apache_Solr_Document $document */
        foreach ($documents as $document) {
            $document->setMultiValue('key', 'foo', true);
        }
    }
}

?>
-----
<?php

namespace Ssch\TYPO3Rector\Tests\Rector\Extensions\solr\v9\ApacheSolrDocumentToSolariumDocumentRector\Fixture;

use ApacheSolrForTypo3\Solr\System\Solr\Document\Document;

class MySolrIndexer
{
    public function __construct()
    {
    }

    public function index()
    {
        $document = new Document();
        $document->addField('key', 'foo', true);
    }

    public function multiple(array $documents)
    {
        /** @var Apache_Solr_Document $document */
        foreach ($documents as $document) {
            $document->addField('key', 'foo', true);
        }
    }
}

?>
