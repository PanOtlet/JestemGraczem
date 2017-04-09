<?php

namespace AppBundle\Model;


class YouTube
{
    protected $url;
    protected $id;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    protected function parseUrl()
    {
        $temp = parse_url($this->getUrl(), PHP_URL_QUERY);
        parse_str($temp, $videoIdParsed);
        return $videoIdParsed;
    }

    public function getHeaders()
    {
        return get_headers($this->getUrl());
    }

    public function HttpStatus()
    {
        $status = $this->getHeaders();
        return substr($status[0], 9, 3);
    }

    /**
     * @return string
     */
    public function getId()
    {
        $url = $this->parseUrl();
        $this->setId($url);
        return $this->id['v'];
    }

    public function getEmbedUrl()
    {
        return 'https://www.youtube.com/oembed?url=' . $this->getUrl();
    }
}