<?php


class block_cdo_survey extends block_base
{

    const COMPONENT_NAME = 'block_cdo_survey';

    public function has_config(): bool
    {
        return true;
    }

    /**
     * @return void
     * @throws coding_exception
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', self::COMPONENT_NAME);
    }

    /**
     * @return stdClass
     */
    public function get_content(): stdClass
    {
        global $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }
        $PAGE->requires->js_call_amd(
            self::COMPONENT_NAME."/app-lazy",
            'init',
            [
                [
                    'lastname' => $USER->lastname,
                    'firstname' => $USER->firstname,
                    'apiToken' => get_config('block_cdo_survey', 'api_token'),
                    'guidPassportRF' => get_config('block_cdo_survey', 'guid_passport_rf')
                ]
            ]
        );
        $this->content = new stdClass;
        $this->content->text = '<div id="main_app">
                                    <survey ></survey>
                                </div>';

        return $this->content;
    }

    public function applicable_formats(): array
    {
        return ['all' => true];
    }
}
