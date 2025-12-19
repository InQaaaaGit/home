<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

class webinar
{
    /**
     * Получить базовый URL для User API из настроек
     *
     * @return string
     */
    private static function getUserApiUrl(): string
    {
        $url = get_config('mod_webinarru', 'userapi_url');
        return !empty($url) ? rtrim($url, '/') : 'https://userapi.mts-link.ru';
    }

    /**
     * Получить базовый URL для Events API из настроек
     *
     * @return string
     */
    private static function getEventsApiUrl(): string
    {
        $url = get_config('mod_webinarru', 'events_url');
        return !empty($url) ? rtrim($url, '/') : 'https://events.mts-link.ru';
    }

    public static function createEvent($token, $name, $startsAt, $duration, $access, $type, $timezone = '1')
    { // Передаются API-токен, имя и время начала мероприятия (тип Date)
        global $CFG;
        require_once $CFG->libdir . '/filelib.php';
        $params = [
            'name' => $name,
            'access' => $access,
            'description' => '', //TODO Сделать из настроек при создании конференции
            'startsAt[date][year]' => $startsAt->format('Y'),
            'startsAt[date][month]' => $startsAt->format('n'),
            'startsAt[date][day]' => $startsAt->format('j'),
            'startsAt[time][hour]' => $startsAt->format('G'),
            'startsAt[time][minute]' => abs($startsAt->format('i')),
            'timezone' => $timezone,
            'type' => $type,
            'lang' => 'RU',
            'duration' => $duration,
        ];

        $curl = new Curl();
        $curl->setHeader(["x-auth-token: $token"]);

        $userApiUrl = self::getUserApiUrl();
        $resp = $curl->post($userApiUrl . '/v3/events', $params);
        $http_code = $curl->get_info()['http_code'];
        if (empty($curl->error)) {
            $curl->post($userApiUrl . '/v3/events/' . $curl->response->eventId . '/sessions');

            if ($curl->error) {
                return json_encode(null);
            } else {
                return json_encode(['id' => $curl->response->eventSessionId, 'link' => $curl->response->link]);
            }
        } else {
            throw new \Exception($resp . $token, $http_code);
            #return json_encode(null);
        }
    }

    public static function registerParticipants($token, $eventSessionId, $params)
    { // Передаются API-токен, и массив с данными участников
        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->post($userApiUrl . '/v3/eventsessions/' . $eventSessionId . '/invite', $params);

        if ($curl->error) {
            return json_encode(null);
        } else {
            return json_encode($curl->response);
        }
    }

    public static function getEventData($token, $eventSessionId)
    { // Передаются API-токен и ID-мероприятия (вебинара - eventSession)
        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->get($userApiUrl . '/v3/eventsessions/' . $eventSessionId);
        if ($curl->error) {
            return json_encode(null);
        } else {
            $response = [
                'id' => $curl->response->id,
                'status' => $curl->response->status,
                'name' => $curl->response->name,
                'startsAt' => $curl->response->startsAt,
                'endsAt' => $curl->response->endsAt,
            ];

            $files = $curl->response->files;
            foreach ($files as $file) {
                if ($file->type == 'file' && $file->fileType == 'record') {
                    $response['recordId'] = $file->id;
                    $response['recordDuration'] = $file->duration;
                }
            }
            return json_encode($response);
        }
    }

    public static function changeEvent($token, $eventSessionId, $name, $startsAt, $duration)
    { // Передаются API-токен и ID-мероприятия (вебинара - eventSession)
        $params = [
            'name' => $name,
            'startsAt[date][year]' => $startsAt->format('Y'),
            'startsAt[date][month]' => $startsAt->format('n'),
            'startsAt[date][day]' => $startsAt->format('j'),
            'startsAt[time][hour]' => $startsAt->format('G'),
            'startsAt[time][minute]' => abs($startsAt->format('i')),
            'duration' => $duration,
        ];

        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->put($userApiUrl . '/v3/organization/events/' . self::getTemplateId($token, $eventSessionId), $params);
        $curl->put($userApiUrl . '/v3/eventsessions/' . $eventSessionId, $params);
        if ($curl->error) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }

    public static function deleteEvent($token, $eventSessionId)
    { // Передаются API-токен и ID-мероприятия (вебинара - eventSession)
        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->put($userApiUrl . '/v3/eventsessions/' . $eventSessionId . '/stop');
        $curl->delete($userApiUrl . '/v3/eventsessions/' . $eventSessionId);
        if ($curl->error) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }

    public static function convertRecord($token, $recordId)
    { // Передаются API-токен и ID-записи
        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->post($userApiUrl . '/v3/records/' . $recordId . '/conversions', ['quality' => '1080']);
        if ($curl->error) {
            return json_encode(null);
        } else {
            return json_encode($curl->response->id);
        }
    }

    public static function getConversionState($token, $fileId)
    { // Передаются API-токен и ID-файла (конвертации)
        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->get($userApiUrl . '/v3/records/conversions/' . $fileId);
        if ($curl->error) {
            return json_encode(null);
        } else {
            return json_encode($curl->response->state);
        }
    }

    public static function getAccountData($email, $password)
    { // Передаются E-Mail и пароль учетной записи
        $curl = new Curl();
        $curl->setBasicAuthentication($email, $password); // Зачем setBasicAuthentication???

        $eventsApiUrl = self::getEventsApiUrl();
        $curl->post($eventsApiUrl . '/api/login', ['email' => $email, 'password' => $password, 'rememberMe' => 'false']);
        if ($curl->error) {
            return json_encode(null);
        } else {
            $sessionId = $curl->getCookie('sessionId');
        }

        $curl->setCookie('sessionId', $sessionId);
        $curl->get($eventsApiUrl . '/api/login');

        if ($curl->error) {
            return json_encode(null);
        } else {
            return json_encode(['usedSize' => $curl->response->fileSize->used, 'maxSize' => $curl->response->fileSize->max]);
        }
    }

    private static function getTemplateId($token, $eventSessionId)
    {
        $from = DateTime::createFromFormat('Y-m-d\TG:i:s+O', json_decode(self::getEventData($token, $eventSessionId))->startsAt);

        $params = [
            'from' => $from->format('Y-m-d'),
            'status[0]' => 'ACTIVE',
            'status[1]' => 'STOP',
            'status[2]' => 'START',
        ];

        $curl = new Curl();
        $curl->setHeader('x-auth-token', $token);

        $userApiUrl = self::getUserApiUrl();
        $curl->get($userApiUrl . '/v3/organization/events/schedule', $params);
        if ($curl->error) {
            return json_encode(null);
        } else {
            $response = $curl->response;
            foreach ($response as $event) {
                $eventSessions = $event->eventSessions;
                foreach ($eventSessions as $eventSession) {
                    if ($eventSession->id == $eventSessionId) {
                        return json_encode($event->id);
                    }
                }
            }
        }
    }
}
