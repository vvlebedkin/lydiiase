<?php
class ControllerExtensionModuleBanner extends Controller
{
    public function index($setting)
    {
        static $module = 0;

        $this->load->model('design/banner');
        $this->load->model('tool/image');

        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.min.js');

        $data['banners'] = [];

        $results = $this->model_design_banner->getBanner($setting['banner_id']);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $data['banners'][] = [
                    'title' => $result['title'],
                    'link'  => $result['link'],
                    'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
                ];
            }
        }

        $data['module'] = $module++;

        // Определяем имя шаблона на основе названия модуля
// Убираем пробелы и переводим в нижний регистр для безопасности
        $template_name = strtolower(str_replace(' ', '_', $setting['name']));

// Проверяем, существует ли файл с таким именем в папке твоей темы
        if (is_file(DIR_TEMPLATE . $this->config->get('theme_default_directory') . '/template/extension/module/banner_' . $template_name . '.twig')) {
            return $this->load->view('extension/module/banner_' . $template_name, $data);
        } else {
            // Если специального шаблона нет, используем стандартный
            return $this->load->view('extension/module/banner', $data);
        }
    }
}
