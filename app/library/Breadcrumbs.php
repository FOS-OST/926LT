<?php
/**
 * Breadcrumbs.php
 * Library\Breadcrumbs
 *
 * Handles the breadcrumbs for the application
 */


class Breadcrumbs
{
    /**
     * Keeps all the breadcrumbs
     *
     * @var array
     */
    private $elements = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->elements[] = [
            'link' => '/dashboard',
            'text' => 'Trang chủ',
        ];
    }
    /**
     * Adds a new element in the stack
     *
     * @param string $caption
     * @param string $link
     */
    public function add($caption, $link = null)
    {
        $element = [
            'link' => is_null($link) ? $link : '/' . $link,
            'text' => $caption,
        ];

        array_unshift($this->elements, $element);
    }

    /**
     * Returns all the elements back to the caller
     *
     * @return string
     */
    public function generate()
    {
        return $this->elements;
    }
}