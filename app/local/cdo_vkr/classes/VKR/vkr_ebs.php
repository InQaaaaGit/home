<?php

namespace local_cdo_vkr\VKR;

use PDO;
use DateTimeZone;
use DateTimeImmutable ;
class vkr_ebs
{
    const PATH = "E:\iis\FullTextDocStud\\";
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $DB;
    /**
     * @var mixed
     */
    private $data;

    public function __construct($ccp)
    {
        $this->host = '10.2.225.165\MSSQLCYR';
        $this->dbname = 'studworks';
        $this->username = 'portal';
        $this->password = 'fkjv%gFcw';
        $this->connect();
        $this->data = $ccp;
    }

    protected function connect()
    {
        $dbh = new PDO(
            "sqlsrv:Server={$this->host};Database={$this->dbname}",
            $this->username,
            $this->password
        );
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->DB = $dbh;
    }

    public function create_data_of_VKR()
    {
        $this->DB->beginTransaction();
        $ID_MO = $this->create_MOBJECT_row();
        $DOC_ID = $this->create_DOC_row();
        if (!empty($ID_MO) && !empty($DOC_ID)) {
            $this->create_DOCRES_row((int)$DOC_ID, (int)$ID_MO);
            $this->create_stwk_row((int)$DOC_ID);
        } else {
            $this->DB->rollBack();
            return [
                'status' => false,
                //Данные для таблицы MOBJECT или DOC не созданы
                'message' => get_string('not_created_mega_pro_record', 'local_cdo_vkr')
            ];
        }

        return ['status' => $this->DB->commit(), 'message' => 'good or bad commit'];
    }

    public function create_stwk_row($id)
    {

        $SQL = "INSERT INTO [dbo].[STWK_WORKS]
                           ([DOC_ID]
                           ,[NAME]
                           ,[STUDENT]
                           ,[FACULTY]
                           ,[CATHEDRA]
                           ,[DISCIPLINE]
                           ,[COURSE]
                           ,[GRP]
                           ,[SEMESTER]
                           ,[WYEAR]
                           ,[EDFORM]
                           ,[PROGNAME]
                           ,[SPECIALITY]
                           ,[PROFILE]
                           ,[TRAINLEVEL]
                           ,[WORKTYPE]
                           ,[KEYWORDS]
                           ,[ID_MO]
                           ,[TID]
                           ,[TEACHER]
                           ,[CUSTOM1]
                           ,[CUSTOM2]
                           ,[CUSTOM3]
                           ,[CUSTOM4]
                           ,[CUSTOM5]
                           ,[CUSTOM6]
                           ,[CUSTOM7]
                           ,[CUSTOM8]
                           ,[CUSTOM9]
                           ,[CRDATE])
                     VALUES
                           ($id
                           ,'{$this->data->theme_name}'
                           ,'{$this->data->fio}'
                           ,'{$this->data->edu_division}'
                           ,'{$this->data->edu_lectern}'
                           ,NULL
                           ,'{$this->data->student_course}'
                           ,'{$this->data->edu_group}'
                           ,NULL
                           ,'{$this->data->year}'
                           ,'{$this->data->edu_form}'
                           ,'{$this->data->edu_specialization}'
                           ,'{$this->data->edu_specialization}'
                           ,'{$this->data->edu_profile}'
                           ,'{$this->data->edu_level}'
                           ,N'Дипломная'
                           ,'{$this->data->key}'
                           ,NULL
                           ,2147
                           ,'{$this->data->fio_manager}'
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,NULL
                           ,CAST(GETDATE() as float))";
        return $this->DB->query($SQL);
    }

    public function create_MOBJECT_row()
    {
        $SQL = "SELECT [NAME] FROM [dbo].[MOBJECT] WHERE [NAME]='{$this->data->end_filename}'";
        $record = $this->DB->query($SQL, PDO::FETCH_OBJ);
        $record_exist = false;
        foreach ($record as $item) {
            $record_exist = (bool)($item->NAME);
        }
        if (!$record_exist) {
            $path = self::PATH .
                $this->data->year . '\\' .
                $this->data->end_filename . '.' .
                $this->data->end_filename_type;
            $SQL = "INSERT INTO [dbo].[MOBJECT]
           ([NAME]
           ,[TYP]
           ,[SIZ]
           ,[CRDATE]
           ,[ITEM]
           ,[TXT]
           ,[LINK]
           ,[IDCMD]
           ,[ACC]
           ,[CODE]
           ,[LICENSE]
           ,[LICDATE]
           ,[MSTATE]
           ,[MISUSERATING]
           ,[MISUSELINK]
           ,[IDT]
           ,[BPUBLIC]
           ,[MISUSEID]
           ,[MISUSEIDM]
           ,[IDV]
           ,[CRC])
     VALUES
           ('{$this->data->end_filename}'
           ,'{$this->data->end_filename_type}'
           ,'{$this->data->end_filename_size}'
           ,CAST(GETDATE() as float)
           ,NULL
           ,NULL
           ,'$path'
           ,NULL
           ,NULL
           ,NULL
           ,NULL
           ,NULL
           ,1
           ,NULL
           ,NULL
           ,1
           ,0
           ,NULL
           ,NULL
           ,0
           ,NULL)";
            $this->DB->query($SQL);
            return $this->DB->lastInsertId();
        }
        return false;
    }

    public function get_DOC_rows()
    {
        $SQL = "
          SELECT TOP (1) [DOC_ID]
          FROM [studworks].[dbo].[DOC]
          ORDER BY DOC_ID DESC";
        $result = $this->DB->query($SQL);
        foreach ($result as $item) {
            return ($item['DOC_ID'] + 1);
        }
        throw new \dml_exception('200');
    }

    public function create_DOC_row()
    {
           $last_id = $this->get_DOC_rows();
        date_default_timezone_set('Europe/Ulyanovsk');
        $n=md5($last_id);
        $date = new DateTimeImmutable();
        $addtime=$date->format('Ymdhis.0');
        $rs = chr(30);
        $us = chr(31);
        $marc= "000  {$us}000000nam  2200217   4500{$rs}001  {$us}0$n{$rs}005  {$us}0$addtime{$rs}100  {$us}a{$this->data->fio}{$rs}245  {$us}a{$this->data->theme_name}{$rs}260  {$us}c{$this->data->year}{$rs}}900  {$us}a{$this->data->end_filename}{$rs}930  {$us}iДипломная{$us}f{$this->data->edu_division}{$us}r{$this->data->edu_lectern}{$us}t{$this->data->fio_manager}{$us}c{$this->data->student_course}{$us}g{$this->data->edu_group}{$us}e{$this->data->edu_form}{$us}a{$this->data->edu_specialization}{$us}b{$this->data->edu_profile}{$us}h{$this->data->edu_level}";
        $SQL = "INSERT INTO [studworks].[dbo].[DOC]
                   ([DOC_ID]
                   ,[RECTYPE]
                   ,[BIBLEVEL]
                   ,[ITEM])
                 VALUES
                       ($last_id
                       ,'a'
                       ,'m'
                       ,'qqq')";
        $this->DB->query($SQL);
        file_put_contents('/www/marc', $marc, FILE_APPEND);
        return $last_id;
    }

    public function create_DOCRES_row($DOC_ID, $ID_MO)
    {
        $SQL = "INSERT INTO [dbo].[DOCRES]
                       ([DOC_ID]
                       ,[ID_MO])
                 VALUES
                       ({$DOC_ID}
                       ,{$ID_MO})";
        $this->DB->query($SQL);
    }
}
