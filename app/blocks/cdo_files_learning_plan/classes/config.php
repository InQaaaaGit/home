<?php

abstract class ConfigLearningPlan
{
    private static $pathToSaveFiles = '/www/opop-files';
    private static $authLogin = 'AdminEIOS';
    private static $authPassword = 'OB4bpe6J';

//    private static $mainUrl = 'http://10.1.0.25/cdo_uni5/hs';
      public static $mainUrl = 'http://10.1.0.44/ulsu/hs';
 //   private static $mainUrl = 'http://10.1.0.44/dev_ulsu/hs';

    private static $services = [
        'GetEducationProgram' => '/cdo_eois_Campus/GetEducationProgramInfo',
        'GetFileBinary' => '/cdo_eois_Campus/GetFileBinary',
        'PutEducationProgramFile' => '/cdo_eois_Campus/PutEducationProgramFile',
        'PutDisciplineProgramFile' => '/cdo_eois_Campus/PutDisciplineProgramFile',
        'PutEducationProgramLink' => '/cdo_eois_Campus/PutEducationProgramLink',
        'GetEducationProgramAllFiles' => '/cdo_eois_Campus/GetEducationProgramAllFiles',
    ];

    public static function getAuth()
    {
        return (object)[
            'login' => self::$authLogin,
            'password' => self::$authPassword,
        ];
    }

    /**
     * @return string
     */
    public static function getUrl()
    {
        return self::$mainUrl;
    }

    public static function getServices()
    {
        return self::$services;
    }

    public static function getService($service)
    {
        return array_key_exists($service, self::$services) ? self::$services[$service] : '';
    }

    public static function getPathToSave()
    {
        return self::$pathToSaveFiles;
    }
}
