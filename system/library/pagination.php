<?php
/**
 * @package        OpenCart
 * @author        Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link        https://www.opencart.com
 */

/**
 * Pagination class
 */
class Pagination
{
    public $total      = 0;
    public $page       = 1;
    public $limit      = 20;
    public $num_links  = 8;
    public $url        = '';
    public $text_first = '|&lt;';
    public $text_last  = '&gt;|';
    public $text_next  = '&gt;';
    public $text_prev  = '&lt;';

    /**
     * Стандартный метод для отображения пагинации в админ-панели
     *
     * @return    string
     */
    public function render()
    {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (! (int) $this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $output = '<ul class="pagination">';

        if ($page > 1) {
            $output .= '<li><a href="' . str_replace(['&amp;page={page}', '?page={page}', '&page={page}'], '', $this->url) . '">' . $this->text_first . '</a></li>';
            if ($page - 1 == 1) {
                $output .= '<li><a href="' . str_replace(['&amp;page={page}', '?page={page}', '&page={page}'], '', $this->url) . '">' . $this->text_prev . '</a></li>';
            } else {
                $output .= '<li><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
            }
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end   = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end   = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end   += abs($start) + 1;
                    $start  = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end    = $num_pages;
                }
            }

            if ($start > 1) {
                $output .= '<li><span>...</span></li>';
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= '<li class="active"><span>' . $i . '</span></li>';
                } else {
                    if ($i === 1) {
                        $output .= '<li><a href="' . str_replace(['&amp;page={page}', '?page={page}', '&page={page}'], '', $this->url) . '">' . $i . '</a></li>';
                    } else {
                        $output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
                    }
                }
            }

            if ($end < $num_pages) {
                $output .= '<li><span>...</span></li>';
            }
        }

        if ($page < $num_pages) {
            $output .= '<li><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
            $output .= '<li><a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
        }

        $output .= '</ul>';

        if ($num_pages > 1) {
            return $output;
        } else {
            return '';
        }
    }

    /**
     * Новый метод для отображения пагинации в теме Lydiase
     *
     * @return    string
     */
    public function renderCatalog()
    {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (! (int) $this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $output = '<ul class="pagin">';

        // SVG для стрелок
        $svg_prev = '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M16.1433 21.8237C16.3281 21.8237 16.5172 21.7507 16.659 21.6089C16.9426 21.3253 16.9426 20.8612 16.659 20.5776L6.95234 10.871L16.5172 1.30615C16.8008 1.02256 16.8008 0.558498 16.5172 0.274904C16.2336 -0.00868988 15.7695 -0.00868988 15.4859 0.274904L5.40117 10.3554C5.11758 10.639 5.11758 11.103 5.40117 11.3866L15.6234 21.6089C15.7695 21.755 15.9543 21.8237 16.1433 21.8237Z" fill="black" /> </svg>';
        $svg_next = '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M5.85764 21.8237C5.67287 21.8237 5.48379 21.7507 5.34199 21.6089C5.0584 21.3253 5.0584 20.8612 5.34199 20.5776L15.0486 10.871L5.48379 1.30615C5.20019 1.02256 5.20019 0.558498 5.48379 0.274904C5.76738 -0.00868988 6.23144 -0.00868988 6.51504 0.274904L16.5998 10.3554C16.8834 10.639 16.8834 11.103 16.5998 11.3866L6.37754 21.6089C6.23144 21.755 6.0467 21.8237 5.85764 21.8237Z" fill="black" /> </svg>';

        // Кнопка "Назад"
        if ($page > 1) {
            if ($page - 1 === 1) {
                $output .= '<li><a href="' . str_replace(['&amp;page={page}', '?page={page}', '&page={page}'], '', $this->url) . '">' . $svg_prev . '</a></li>';
            } else {
                $output .= '<li><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $svg_prev . '</a></li>';
            }
        } else {
            $output .= '<li><span>' . $svg_prev . '</span></li>';
        }

        // Номера страниц
        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end   = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end   = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end   += abs($start) + 1;
                    $start  = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end    = $num_pages;
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= '<li><a class="active">' . $i . '</a></li>';
                } else {
                    if ($i === 1) {
                        $output .= '<li><a href="' . str_replace(['&amp;page={page}', '?page={page}', '&page={page}'], '', $this->url) . '">' . $i . '</a></li>';
                    } else {
                        $output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
                    }
                }
            }
        }

        // Кнопка "Вперед"
        if ($page < $num_pages) {
            $output .= '<li><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $svg_next . '</a></li>';
        } else {
            $output .= '<li><span>' . $svg_next . '</span></li>';
        }

        $output .= '</ul>';

        if ($num_pages > 1) {
            return $output;
        } else {
            return '';
        }
    }
}
