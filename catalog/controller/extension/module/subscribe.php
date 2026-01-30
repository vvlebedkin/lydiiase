<?php
class ControllerExtensionModuleSubscribe extends Controller
{
    public function send()
    {
        $json = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // 1. Валидация Email
            if (empty($this->request->post['email']) || ! filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                $json['error'] = 'Пожалуйста, введите корректный E-mail';
            }

            // 2. Валидация чекбокса (согласия)
            if (! isset($this->request->post['agree'])) {
                $json['error'] = 'Необходимо ваше согласие на обработку данных';
            }

            if (! $json) {
                $email = $this->request->post['email'];

                $mail                = new Mail($this->config->get('config_mail_engine')); // Передаем протокол сразу
                $mail->parameter     = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port     = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setReplyTo($email);
                $mail->setSender($this->config->get('config_name'));
                $mail->setSubject('Новая подписка на рассылку');
                $mail->setText("Пользователь подписался на рассылку.\nE-mail: " . $email);

                try {
                    // Добавляем @ перед send(), чтобы подавить системные Warning на локалке
                    @$mail->send();
                    $json['success'] = true;
                } catch (Exception $e) {
                    $json['error'] = 'Ошибка отправки: ' . $e->getMessage();
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
