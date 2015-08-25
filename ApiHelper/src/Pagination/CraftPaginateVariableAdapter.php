<?php
namespace ApiHelper\Pagination;

use League\Fractal\Pagination\PaginatorInterface;
use Craft\ElementCriteriaModel;

class CraftPaginateVariableAdapter implements PaginatorInterface
{
    /**
     * Criteria
     *
     * @var Craft\ElementCriteriaModel
     */
    protected $criteria;

    /**
     * Constructor
     *
     * @param Criteria $criteria
     *
     * @return void
     */
    public function __construct(ElementCriteriaModel $criteria)
    {
        $this->criteria = $criteria;

        $this->total_pages = ceil($this->getTotal() / $this->getPerPage());
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $current_page = isset($this->criteria->page) ? (int) $this->criteria->page : 1;

        return ($current_page > $this->total_pages) ? $this->total_pages : $current_page;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        $last_page = $this->criteria->offset + $this->criteria->limit;

        return (int) ($last_page > $this->total_pages) ? $this->total_pages : $last_page;
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        return (int) $this->criteria->total() - (int) $this->criteria->offset;
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
        return (int) $this->criteria->count();
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return (int) $this->criteria->limit;
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        \Craft::dd($page);
        // return $this->criteria->getPageUrl($page);
    }
}
