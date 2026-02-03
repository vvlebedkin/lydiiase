<?php
class ControllerCommonHeader extends Controller
{
    public function index()
    {
        // Analytics
        $this->load->model('setting/extension');

        $analytics_data = []; // ОБЯЗАТЕЛЬНО инициализируем массив

        $analytics = $this->model_setting_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $analytics_data[] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }

// Теперь implode всегда получит массив, даже если он пустой
        $data['analytics'] = implode('', $analytics_data);

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base']        = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords']    = $this->document->getKeywords();
        $data['links']       = $this->document->getLinks();
        $data['styles']      = $this->document->getStyles();
        $data['scripts']     = $this->document->getScripts('header');
        $data['lang']        = $this->language->get('code');
        $data['direction']   = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

                                                // Меню и Категории
        $this->load->model('catalog/category'); // ОБЯЗАТЕЛЬНО загружаем модель
        $this->load->model('catalog/product');

        $data['categories'] = [];

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Подкатегории (Level 2)
                $children_data = [];
                $children      = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = [
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true,
                    ];

                    $children_data[] = [
                        'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
                    ];
                }

                $data['categories'][] = [
                    'name'     => $category['name'],
                    'children' => $children_data, // Теперь переменная определена! [cite: 319]
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
                ];
            }
        }

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

        $data['home']          = $this->url->link('common/home');
        $data['wishlist']      = $this->url->link('account/wishlist', '', true);
        $data['logged']        = $this->customer->isLogged();
        $data['account']       = $this->url->link('account/account', '', true);
        $data['register']      = $this->url->link('account/register', '', true);
        $data['login']         = $this->url->link('account/login', '', true);
        $data['order']         = $this->url->link('account/order', '', true);
        $data['transaction']   = $this->url->link('account/transaction', '', true);
        $data['download']      = $this->url->link('account/download', '', true);
        $data['logout']        = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout']      = $this->url->link('checkout/checkout', '', true);
        $data['contact']       = $this->url->link('information/contact');
        $data['telephone']     = $this->config->get('config_telephone');

        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search']   = $this->load->controller('common/search');
        $data['cart']     = $this->load->controller('common/cart');
        $data['menu']     = $this->load->controller('common/menu');

        $data['special'] = $this->url->link('product/special'); // Ссылка на акции (часто используется для SALE/Новинок)

// Ссылки на статьи (ID нужно проверить в админке: Каталог -> Статьи)
        $data['about']    = $this->url->link('information/about');                           // Пример ID для "О компании"
        $data['delivery'] = $this->url->link('information/information', 'information_id=6'); // Пример ID для "Оплата и доставка"

        return $this->load->view('common/header', $data);
    }
}
