<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					// Дополнительные изображения
					$images = array();
					if ($product_info['image']) {
						$images[] = array(
							'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
						);
					}
					$additional_images = $this->model_catalog_product->getProductImages($product_info['product_id']);
					foreach ($additional_images as $additional_image) {
						$images[] = array(
							'thumb' => $this->model_tool_image->resize($additional_image['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
						);
					}

					// Категория
					$this->load->model('catalog/category');
					$category_name = '';
					$categories = $this->model_catalog_product->getCategories($product_info['product_id']);
					if ($categories) {
						$category_info = $this->model_catalog_category->getCategory($categories[0]['category_id']);
						if($category_info) {
							$category_name = $category_info['name'];
						}
					}
					
					// Опции (Цвет)
					$colors = array();
					$options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
					foreach ($options as $option) {
						if (utf8_strtolower($option['name']) == 'цвет' || utf8_strtolower($option['name']) == 'color') {
							foreach ($option['product_option_value'] as $option_value) {
								$raw_name = $option_value['name'];
								$color_code = '#ccc'; // По умолчанию
								$color_name = $raw_name;

								if (strpos($raw_name, '|') !== false) {
									$parts = explode('|', $raw_name);
									$color_code = trim($parts[0]);
									$color_name = trim($parts[1]);
								} elseif (preg_match('/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/', $raw_name, $matches)) {
									$color_code = $matches[0];
									$color_name = trim(str_replace($color_code, '', $raw_name));
								}

								$colors[] = array(
									'name' => $color_name,
									'color_code' => $color_code
								);
							}
						}
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$tax_price = (float)$product_info['special'];
					} else {
						$special = false;
						$tax_price = (float)$product_info['price'];
					}
		
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format($tax_price, $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'images'      => $images,
						'category'    => $category_name,
						'colors'      => $colors,
						'name'        => $product_info['name'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}