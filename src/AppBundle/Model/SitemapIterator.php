<?php

namespace AppBundle\Model;

use AppBundle;

use Exporter\Source\SourceIteratorInterface;

class SitemapIterator implements SourceIteratorInterface
{
    protected $key;

    protected $stop;

    protected $current;

    public function __construct($stop = 1000)
    {
        $this->stop = $stop;
    }

    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $this->key++;
        $this->current = array(
            'permalink' => '/web',
            'lastmod' => '',
            'changefreq' => 'weekly',
            'priority' => 0.5
        );
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return $this->key < $this->stop;
    }

    public function rewind()
    {
        $this->key = 0;
    }
}