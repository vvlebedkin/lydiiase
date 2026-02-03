<?php
class ControllerInformationNews extends Controller
{
    public function index()
    {
        $this->load->language('information/information');

        $this->document->setTitle("Новинки");

        $data['breadcrumbs']   = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
        ];
        $data['breadcrumbs'][] = [
            'text' => "Новинки",
            'href' => $this->url->link('information/news'),
        ];

        $data['heading_title'] = "Новинки";

        // Загрузка позиций
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['column_right']   = $this->load->controller('common/column_right');
        $data['content_top']    = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/news', $data));
    }
}
