<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail {

    public function send($to_email, $to_name, $subject, $template, $vars = null) {

       //Recuperer le contenu du template
       $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

         //Recuperation des variables facultatives
            if ($vars != null) {
                foreach ($vars as $key => $value) {
                    $content = str_replace('{{ '.$key.' }}', $value, $content);
                }
            }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "diakitelamine555@gmail.com",
                        'Name' => "La boutique ASSO"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 5922711,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        
        $mj->post(Resources::$Email, ['body' => $body]);

    }

}