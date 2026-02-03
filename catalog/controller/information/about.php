<?php
class ControllerInformationAbout extends Controller
{
    public function index()
    {
        $this->load->language('information/information'); // Можно создать свой языковой файл about.php

        $this->document->setTitle("О компании"); // Замените на $this->language->get('heading_title') если используете языковой файл

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
        ];

        $data['breadcrumbs'][] = [
            'text' => "О компании", // Замените на $this->language->get('heading_title')
            'href' => $this->url->link('information/about'),
        ];

        $data['column_left']    = $this->load->controller('common/column_left');
        $data['column_right']   = $this->load->controller('common/column_right');
        $data['content_top']    = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/about', $data));
    }
}
