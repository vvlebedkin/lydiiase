<?php
class ControllerExtensionModuleLiveSearch extends Controller
{
    public function index()
    {
        $json = [];

        if (isset($this->request->get['filter_name']) && $this->request->get['filter_name'] != '') {
            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $filter_data = [
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 5,
            ];

            $results = $this->model_catalog_product->getProducts($filter_data);

            foreach ($results as $result) {
                // Обработка изображения
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], 80, 80);
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', 80, 80);
                }

                // Цены
                $price   = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $special = false;
                if ((float) $result['special'] > 0) {
                    $special = $price; // Текущая цена становится старой
                    $price   = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                }

                // Категория (берем первую попавшуюся у товара)
                $category_name = '';
                $categories    = $this->model_catalog_product->getCategories($result['product_id']);
                if ($categories) {
                    $this->load->model('catalog/category');
                    $category_info = $this->model_catalog_category->getCategory($categories[0]['category_id']);
                    if ($category_info) {
                        $category_name = $category_info['name'];
                    }
                }

                // --- Получаем цвета и вытаскиваем HEX-код ---
                $colors = array();
                $product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

                foreach ($product_options as $option) {
                    // Проверяем, что это опция "Цвет"
                    if (utf8_strtolower($option['name']) == 'цвет') {
                        foreach ($option['product_option_value'] as $option_value) {
                            $raw_name = $option_value['name'];
                            $color_code = '#ccc'; // По умолчанию
                            $color_name = $raw_name;

                            if (strpos($raw_name, '|') !== false) {
                                // Если есть разделитель, разбиваем строку
                                $parts = explode('|', $raw_name);
                                $color_code = trim($parts[0]);
                                $color_name = trim($parts[1]);
                            } elseif (preg_match('/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/', $raw_name, $matches)) {
                                // Если разделителя нет, но есть HEX-код (старая схема)
                                $color_code = $matches[0];
                                $color_name = trim(str_replace($color_code, '', $raw_name));
                            }

                            $colors[] = array(
                                'name'       => $color_name,
                                'color_code' => $color_code
                            );
                        }
                    }
                }

                $json[] = [
                    'name'     => $result['name'],
                    'price'    => $price,
                    'special'  => $special,
                    'category' => $category_name,
                    'image'    => $image,
                    'href'     => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                    'colors'   => $colors, // Добавляем цвета в JSON
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
