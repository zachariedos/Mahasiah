<?php

namespace App\Entity;

class Search
{

    /**
     * @var string|null
     */
    private $searchPrixMin;

    /**
     * @return string|null
     */
    public function getSearchPrixMin(): ?string
    {
        return $this->searchPrixMin;
    }

    /**
     * @param string|null $search
     * @return Search
     */
    public function setSearchPrixMin(string $search): Search
    {
        $this->searchPrixMin = $search;
        return $this;
    }

    /**
     * @var string|null
     */
    private $searchPrixMax;

    /**
     * @return string|null
     */
    public function getSearchPrixMax(): ?string
    {
        return $this->searchPrixMax;
    }

    /**
     * @param string|null $search
     * @return Search
     */
    public function setSearchPrixMax(string $search): Search
    {
        $this->searchPrixMax = $search;
        return $this;
    }

    /**
     * @var string|null
     */
    private $searchGlobal;

    /**
     * @return string|null
     */
    public function getSearchGlobal(): ?string
    {
        return $this->searchGlobal;
    }

    /**
     * @param string|null $search
     * @return Search
     */
    public function setSearchGlobal(string $search): Search
    {
        $this->searchGlobal = $search;
        return $this;
    }
}
