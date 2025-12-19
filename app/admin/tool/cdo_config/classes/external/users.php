<?php

namespace tool_cdo_config\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_user_external;
use GuzzleHttp\Exception\GuzzleException;

use invalid_parameter_exception;
use moodle_exception;
use stdClass;
use tool_cdo_config\helpers\demo_accounts;

class users extends external_api
{
    const PLUGIN_NAME = 'tool_cdo_config';
    const USER_SENDER = 96575;

    public static function user_update_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_INT, 'user_id', VALUE_REQUIRED),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_DEFAULT, ''),
            ],
            '',
            VALUE_OPTIONAL);
    }

    /**
     * @throws moodle_exception
     */
    public static function user_update($user_id, $description = '')
    {
        global $CFG;
        require_once($CFG->dirroot . "/user/lib.php");
        $user = new stdClass();
        $user->id = $user_id;
        $user->description = $description;
        user_update_user($user, false, false);
    }

    public static function user_update_returns()
    {
        return null;
    }

    public static function create_demo_account_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'firstname' => new external_value(PARAM_TEXT, 'firstname', VALUE_REQUIRED),
                'lastname' => new external_value(PARAM_TEXT, 'lastname', VALUE_REQUIRED, ''),
                'middlename' => new external_value(PARAM_TEXT, 'middlename', VALUE_DEFAULT, ''),
                'email' => new external_value(PARAM_TEXT, 'email', VALUE_DEFAULT, ''),
                'city' => new external_value(PARAM_TEXT, 'city', VALUE_DEFAULT, 'Москва'),
                'parallel' => new external_value(PARAM_INT, 'parallel 1-11 integer', VALUE_DEFAULT, 1),
                'tranid' => new external_value(PARAM_RAW, 'tranid', VALUE_DEFAULT, ''),
                'formid' => new external_value(PARAM_RAW, 'formid', VALUE_DEFAULT, ''),
                'bitrix_id' => new external_value(PARAM_TEXT, 'bitrix_id', VALUE_DEFAULT, ''),
                'use_html' => new external_value(PARAM_BOOL, 'use_html', VALUE_DEFAULT, false),
                'is_self_create' => new external_value(PARAM_BOOL, 'is_self_create', VALUE_DEFAULT, false),
                'auth' => new external_single_structure([
                    'domain' => new external_value(PARAM_TEXT, 'domain', VALUE_DEFAULT, ''),
                    'client_endpoint' => new external_value(PARAM_TEXT, 'domain', VALUE_DEFAULT, ''),
                    'server_endpoint' => new external_value(PARAM_TEXT, 'domain', VALUE_DEFAULT, ''),
                    'member_id' => new external_value(PARAM_TEXT, 'domain', VALUE_DEFAULT, ''),
                ], '', VALUE_DEFAULT, []),
                'document_id' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'bitrix', VALUE_DEFAULT, ''),
                    '',
                    VALUE_DEFAULT, []
                ),
            ],
            '',
            VALUE_OPTIONAL);
    }

    /**
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws GuzzleException
     */
    public static function create_demo_account(
        $firstname, $lastname, $middlename, $email,
        $city, $parallel, $train_id, $form_id,
        $bitrix_id, $use_html, $is_self_create): array
    {
        global $CFG, $USER;
        require_once $CFG->dirroot . "/user/externallib.php";
        require_once $CFG->dirroot . "/user/lib.php";
        require_once $CFG->dirroot . "/cohort/lib.php";

        $user_object = new stdClass();
        $user_object->firstname = $firstname;
        $user_object->lastname = $lastname;
        $user_object->middlename = $middlename;
        $user_object->email = $email;
        $user_object->password = generate_password();
        $user_object->city = $city;
        $user_object->customfields[0]['value'] = $bitrix_id;
        $user_object->customfields[0]['type'] = 'bitrix_lead_id';
        /* вместо БД и прочего - используем админское поле как глобальный итератор для логинов */
        $login_number = (int)$USER->alternatename;
        $login_number++;
        $USER->alternatename = $login_number;
        if ($is_self_create) {
            $user_object->email = "self_create@testag.ru";
        } else {
            $user_object->email = $email;
        }
        user_update_user($USER);
        /* --- */
        $user_object->username = "demo$login_number";
        $users[] = (array)$user_object;
        $result = core_user_external::create_users($users);
        $result[0]['password'] = $user_object->password;
        $user_sender_id = $result[0]['id'];
        cohort_add_member(
            demo_accounts::get_cohort_mapping($parallel),
            $user_sender_id
        );
        $user_object_to_send = get_complete_user_data('id', $user_sender_id);
        $user_object_sender = get_complete_user_data('id', self::USER_SENDER); // const
        $html = '<div dir="ltr" class="es-wrapper-color" lang="ru" style="background-color:#FFFFFF"><!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#ffffff"></v:fill>
			</v:background>
		<![endif]-->
   <table width="100%" cellspacing="0" cellpadding="0" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FFFFFF">
     <tr>
      <td valign="top" style="padding:0;Margin:0">
       <table cellpadding="0" cellspacing="0" align="center" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
         <tr>
          <td align="center" background="https://frhvgpv.stripocdn.email/content/guids/CABINET_28c867a2e018789d3bc3b2b8bc98978701ae96cc036b28baf216b103906557e4/images/graphic_composition_9_1_n8L.jpg" style="padding:0;Margin:0;background-image:url(https://frhvgpv.stripocdn.email/content/guids/CABINET_28c867a2e018789d3bc3b2b8bc98978701ae96cc036b28baf216b103906557e4/images/graphic_composition_9_1_n8L.jpg);background-repeat:no-repeat;background-position:center;background-size:contain">
           <table bgcolor="#efefef" align="center" cellpadding="0" cellspacing="0" class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#EFEFEF;border-radius:20px 20px 0 0;width:600px" role="none">
             <tr>
              <td align="left" style="padding:0;Margin:0;padding-top:40px;padding-right:40px;padding-left:40px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" style="padding:0;Margin:0;font-size:0px"><a target="_blank" href="https://viewstripo.email" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2D3142;font-size:18px"><img src="https://static.tildacdn.com/tild6536-3030-4165-b533-663535353364/Group_5.png" alt="Logo" height="60" title="Logo" class="adapt-img" style="display:block;font-size:18px;border:0;outline:none;text-decoration:none"></a></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
              <td align="left" style="padding:0;Margin:0;padding-right:40px;padding-left:40px;padding-top:20px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#fafafa" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;background-color:#fafafa;border-radius:10px" role="presentation">
                     <tr>
                      <td align="left" class="es-text-4206" style="padding:20px;Margin:0"><h3 style="Margin:0;font-family:Imprima, Arial, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:28px;font-style:normal;font-weight:bold;line-height:33.6px;color:#2D3142">Добрый день {Имя пользователя},</h3><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br></p><h1 style="Margin:0;font-family:Imprima, Arial, sans-serif;mso-line-height-rule:exactly;letter-spacing:0;font-size:48px;font-style:normal;font-weight:bold;line-height:57.6px;color:#2D3142"><span class="es-text-mobile-size-20" style="line-height:24px !important;font-size:20px">Спасибо за регистрацию на демо-доступ.</span></h1><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br>Почувствуйте себя учеником онлайн школы Академической гимназии!</p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br></p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px">Ваш логин для входа:</p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br></p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px">{Логин}</p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br></p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px">Пароль:</p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px"><br></p><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px">{Пароль}<br><br>Зайти в личный кабинет вы можете по ссылке ниже:</p></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
              <td align="left" style="Margin:0;padding-right:40px;padding-left:40px;padding-top:30px;padding-bottom:20px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="center" valign="top" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" style="padding:0;Margin:0"><!--[if mso]><a href="https://URL.com" target="_blank" hidden>
	<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" esdevVmlButton href="https://URL.com" style="height:56px; v-text-anchor:middle; width:520px" arcsize="50%" stroke="f"  fillcolor="#005eb8">
		<w:anchorlock></w:anchorlock>
		<center style="color:#ffffff; font-family:Imprima, Arial, sans-serif; font-size:22px; font-weight:700; line-height:22px;  mso-text-raise:1px">Войти в Демо кабинет</center>
	</v:roundrect></a>
<![endif]--><!--[if !mso]><span class="es-button-border msohide" style="border-style:solid;border-color:#2CB543;background:#005eb8;border-width:0px;display:block;border-radius:30px;width:auto;mso-hide:all"><a href="https://URL.com" target="_blank" class="es-button msohide" style="mso-style-priority:100 !important;text-decoration:none !important;mso-line-height-rule:exactly;color:#FFFFFF;font-size:22px;padding:15px 20px 15px 20px;display:block;background:#005eb8;border-radius:30px;font-family:Imprima, Arial, sans-serif;font-weight:bold;font-style:normal;line-height:26.4px;width:auto;text-align:center;letter-spacing:0;mso-padding-alt:0;mso-border-alt:10px solid #005eb8;mso-hide:all;border-left-width:5px;border-right-width:5px;border-color:#7630f3">Войти в Демо кабинет</a></span><!--<![endif]--></td>
                     </tr>
                     <tr>
                      <td align="center" style="padding:0;Margin:0;padding-top:15px"><!--[if mso]><a href="https://URL.com" target="_blank" hidden>
	<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" esdevVmlButton href="https://URL.com" style="height:56px; v-text-anchor:middle; width:520px" arcsize="50%" stroke="f"  fillcolor="#009fdf">
		<w:anchorlock></w:anchorlock>
		<center style="color:#ffffff; font-family:Imprima, Arial, sans-serif; font-size:22px; font-weight:700; line-height:22px;  mso-text-raise:1px">Пройти полную регистрацию</center>
	</v:roundrect></a>
<![endif]--><!--[if !mso]><!-- --><span class="es-button-border msohide" style="border-style:solid;border-color:#2CB543;background:#009FDF;border-width:0px;display:block;border-radius:30px;width:auto;mso-hide:all"><a href="https://URL.com" target="_blank" class="es-button msohide" style="mso-style-priority:100 !important;text-decoration:none !important;mso-line-height-rule:exactly;color:#FFFFFF;font-size:22px;padding:15px 20px 15px 20px;display:block;background:#009FDF;border-radius:30px;font-family:Imprima, Arial, sans-serif;font-weight:bold;font-style:normal;line-height:26.4px;width:auto;text-align:center;letter-spacing:0;mso-padding-alt:0;mso-border-alt:10px solid #009FDF;mso-hide:all;border-color:#7630f3;border-left-width:5px;border-right-width:5px">Пройти полную регистрацию</a></span><!--<![endif]--></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
              <td align="left" style="Margin:0;padding-right:40px;padding-left:40px;padding-top:10px;padding-bottom:10px">
               <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                 <tr>
                  <td align="left" style="padding:0;Margin:0;width:520px">
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="center" style="padding:0;Margin:0"><p style="Margin:0;mso-line-height-rule:exactly;font-family:Imprima, Arial, sans-serif;line-height:27px;letter-spacing:0;color:#2D3142;font-size:18px">Академическая Гимназия © 2024&nbsp;</p></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
           </table></td>
         </tr>
       </table>
       <table cellpadding="0" cellspacing="0" align="center" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
         <tr>
          <td align="center" style="padding:0;Margin:0">
           <table bgcolor="#efefef" align="center" cellpadding="0" cellspacing="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#EFEFEF;width:600px">
           </table></td>
         </tr>
       </table></td>
     </tr>
   </table>
  </div>';
        if (!$is_self_create) {
            $result_email = email_to_user(
                $user_object_to_send,
                $user_object_sender,
                get_string('demo_account_email_subject', self::PLUGIN_NAME),
                get_string('demo_account_email_messagetext', 'tool_cdo_config',
                    [
                        'login' => $user_object->username,
                        'password' => $user_object->password
                    ]
                ),
                $use_html ? $html : ''

            );
            $result[0]['email_send_status'] = $result_email;
        } else {

            /*$client = new Client([
                'base_uri' => 'https://acsc.bitrix24.ru/rest/1320/wsywcpi1mia7e2si/',
            ]);*/

            $get = "TEMPLATE_ID=1132&DOCUMENT_ID[0]=crm&DOCUMENT_ID[1]=CCrmDocumentLead&DOCUMENT_ID[2]=$bitrix_id&PARAMETERS[p_login]=$user_object->username&PARAMETERS[p_pass]=$user_object->password";
            require_once $CFG->libdir . '/filelib.php';
            $curl = new \curl();
            $response = $curl->get('https://acsc.bitrix24.ru/rest/1320/wsywcpi1mia7e2si/bizproc.workflow.start?'.$get);
            if ($curl->info['http_code'] != 200) {

            }
            // $result_bitrix = json_decode($response);
            // $response = $client->get('bizproc.workflow.start' . $get);
            $result[0]['result'] = json_decode($response, true)['result'];
        }
        return $result[0];
    }

    public static function create_demo_account_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'id section', VALUE_REQUIRED),
                'username' => new external_value(PARAM_TEXT, 'course', VALUE_REQUIRED),
                'password' => new external_value(PARAM_TEXT, 'section', VALUE_REQUIRED),
                'email_send_status' => new external_value(PARAM_BOOL, 'email_send_status', VALUE_DEFAULT, false),
                'result' => new external_value(PARAM_TEXT, 'result', VALUE_DEFAULT, ''),
            ]
        );
    }

}