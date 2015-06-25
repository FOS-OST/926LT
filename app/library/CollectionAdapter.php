<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
class CollectionAdapter extends Paginator {
    /**
     * @var \Phalcon\Mvc\CollectionInterface
     */
    private $model;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->model = $options['model'];
        $this->page = $options['page'];
        $this->limit = $options['limit'];
    }

    /**
     * @param int $page
     */
    public function setCurrentPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return \stdClass
     */
    public function getPaginate()
    {
        $totalItems = call_user_func([$this->model, 'count']);
        $totalPages = intval(ceil($totalItems / $this->limit));
        $items = call_user_func(
            [$this->model, 'find'],
            [
                'limit' => $this->limit,
                'skip' => $this->limit * ($this->page - 1)
            ]
        );

        $before = null;

        if ($this->page > 1) {
            $before = $this->page - 1;
        }

        $next = $this->page + 1;

        if ($next > $totalPages) {
            $next = null;
        }

        return (object) [
            'items' => $items,
            'current' => $this->page,
            'before' => $before,
            'next' => $next,
            'last' => $totalPages,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
        ];
    }
}
