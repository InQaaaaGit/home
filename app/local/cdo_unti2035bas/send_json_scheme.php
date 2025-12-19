<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_cdo_unti2035bas\infrastructure\xapi\client;

require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/local/cdo_unti2035bas/send_json_scheme.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Отправка JSON схемы');
$PAGE->set_heading('Отправка JSON схемы в xAPI');

/**
 * Форма для отправки произвольного JSON в xAPI
 */
class json_send_form extends moodleform {
    
    public function definition(): void
    {
        $mform = $this->_form;
        
        // Поле для ввода JSON
        $mform->addElement(
            'textarea', 
            'json_data', 
            'JSON данные',
            [
                'rows' => 20,
                'wrap' => 'soft'
            ]
        );
        $mform->addRule('json_data', 'Это поле обязательно для заполнения', 'required', null, 'client');
        $mform->setType('json_data', PARAM_RAW);
        
        // Кнопки
        $this->add_action_buttons(true, 'Отправить JSON');
    }
    
    public function validation($data, $files): array
    {
        $errors = parent::validation($data, $files);
        
        // Проверяем валидность JSON
        if (!empty($data['json_data'])) {
            $decoded = json_decode($data['json_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors['json_data'] = 'Некорректный JSON: ' . json_last_error_msg();
            }
        }
        
        return $errors;
    }
}

// Обработка формы
$form = new json_send_form();

$success_message = '';
$error_message = '';

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/cdo_unti2035bas/streams.php'));
} else if ($data = $form->get_data()) {
    try {
        // Получаем настройки xAPI
        $endpoint = get_config('local_cdo_unti2035bas', 'xapiendpoint');
        $key = get_config('local_cdo_unti2035bas', 'xapikey');
        $secret = get_config('local_cdo_unti2035bas', 'xapisecret');
        
        // Создаем xAPI клиент напрямую
        $xapi_client = new client($endpoint, $key, $secret);
        
        // Декодируем JSON
        $json_array = json_decode($data->json_data, true);
        
        // Отправляем данные (всегда в тестовом режиме для произвольного JSON)
        $result = $xapi_client->send($json_array, true);
        
        $success_message = 'JSON успешно отправлен! Результат: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        $error_message = 'Ошибка при отправке JSON: ' . $e->getMessage();
    }
}

// Вывод страницы
echo $OUTPUT->header();

// Добавляем простые CSS стили
echo '<style>
textarea#id_json_data {
    font-family: Consolas, Monaco, "Courier New", monospace !important;
    font-size: 14px !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    resize: vertical !important;
}

.mform fieldset {
    margin-bottom: 20px !important;
}

.fitem_ftext textarea {
    margin-bottom: 15px !important;
}

.fitem_actionbuttons {
    margin-top: 20px !important;
    padding-top: 15px !important;
    border-top: 1px solid #e3e3e3;
    clear: both !important;
}
</style>';

if (!empty($success_message)) {
    echo $OUTPUT->notification($success_message, 'success');
}

if (!empty($error_message)) {
    echo $OUTPUT->notification($error_message, 'error');
}



// Отображаем форму
$form->display();

echo $OUTPUT->footer();

