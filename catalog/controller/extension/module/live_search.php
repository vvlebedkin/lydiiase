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

                $json[] = [
                    'name'     => $result['name'],
                    'price'    => $price,
                    'special'  => $special,
                    'category' => $category_name,
                    'image'    => $image,
                    'href'     => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
