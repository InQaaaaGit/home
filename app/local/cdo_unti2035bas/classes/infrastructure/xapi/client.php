<?php

namespace local_cdo_unti2035bas\infrastructure\xapi;

use local_cdo_unti2035bas\exceptions\curl_error;
use local_cdo_unti2035bas\exceptions\xapi_error;
use local_cdo_unti2035bas\infrastructure\moodle\xapi_results_repository;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class client
{
    private string $endpoint;
    private string $key;
    private string $secret;
    private xapi_results_repository $resultsRepository;

    public function __construct(string $endpoint, string $key, string $secret)
    {
        $this->endpoint = rtrim($endpoint, '/');
        $this->key = $key;
        $this->secret = $secret;
        $this->resultsRepository = new xapi_results_repository();
    }

    /**
     * @param array<statement_schema> $statements
     * @return array<string>
     */
    public function send(array $statements, $test = false, $send_json = false): array
    {
        $curl = curl_init();
        $url = "{$this->endpoint}/statements";
        if (!$send_json) {
            if (!$test)
                $payload = array_map(fn($s) => $s->dump(), $statements);
            else
                $payload = $statements;
        } else {
            $payload = $statements;
        }
        $query_json = json_encode($payload);

        curl_setopt_array($curl, [
            CURLOPT_FAILONERROR => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Cache-Control: no-cache',
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Experience-API-Version: 1.0.3',
            ],
            CURLOPT_USERPWD => "{$this->key}:{$this->secret}",
            CURLOPT_POSTFIELDS => $query_json,
        ]);

        /** @var string $text */
        $text = curl_exec($curl);
        $info = curl_getinfo($curl);
        $curlerrno = curl_errno($curl);

        // Сохраняем результат запроса
        $result_json = json_encode([
            'status_code' => $info['http_code'],
            'response' => $text,
            'curl_error' => $curlerrno ? curl_error($curl) : null,
            'curl_errno' => $curlerrno,
            'info' => $info
        ]);

        $this->resultsRepository->save_result($result_json, $query_json);

        if ($curlerrno != 0) {
            throw new curl_error(curl_error($curl), $curlerrno);
        }
        /** @var array<string, string>|array<mixed> $data */
        $data = json_decode($text, true) ?: [];
        $statuscode = $info['http_code'];
        if ($statuscode != 200) {
            throw new xapi_error($statuscode, $text, $data);
        }
        return $data;
    }

    /**
     * @param string $lrid
     * @return array<string, mixed>
     */
    public function download(string $lrid): array
    {
        $curl = curl_init();
        $query = http_build_query(['statementId' => $lrid], '', '&', PHP_QUERY_RFC3986);
        $url = "{$this->endpoint}/statements?{$query}";

        curl_setopt_array($curl, [
            CURLOPT_FAILONERROR => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Cache-Control: no-cache',
                'Accept: application/json',
                'X-Experience-API-Version: 1.0.3',
            ],
            CURLOPT_USERPWD => "{$this->key}:{$this->secret}",
        ]);

        /** @var string $text */
        $text = curl_exec($curl);
        $info = curl_getinfo($curl);
        $curlerrno = curl_errno($curl);

        // Сохраняем результат запроса
        $query_json = json_encode(['url' => $url, 'lrid' => $lrid]);
        $result_json = json_encode([
            'status_code' => $info['http_code'],
            'response' => $text,
            'curl_error' => $curlerrno ? curl_error($curl) : null,
            'curl_errno' => $curlerrno,
            'info' => $info
        ]);

        $this->resultsRepository->save_result($result_json, $query_json);

        if ($curlerrno != 0) {
            throw new curl_error(curl_error($curl), $curlerrno);
        }
        /** @var array<string, mixed> $data */
        $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        $statuscode = $info['http_code'];
        if ($statuscode != 200) {
            throw new xapi_error($statuscode, $text, $data);
        }
        return $data;
    }
}
