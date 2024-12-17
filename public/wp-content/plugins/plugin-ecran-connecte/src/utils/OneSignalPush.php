<?php

namespace Utils;

include_once __DIR__ . '/../../config-notifs.php';

use Models\CodeAde;

class OneSignalPush
{
    /**
     * OneSignalPush constructor.
     */
    function __construct() {

    }

    /**
     * Envoie une notification aux cibles spécifiées via l'API OneSignal.
     *
     * Si aucune cible spécifique n'est définie, la notification est envoyée à tous les utilisateurs.
     * Si des cibles sont spécifiées, elles sont converties en filtres basés sur leurs codes ADE et
     * la notification est envoyée uniquement à ces utilisateurs.
     *
     * @version 1.0
     * @date 15 Octobre 2024
     *
     * @param array|null $targets  Un tableau d'objets cibles (avec des codes ADE), ou null pour inclure tous les utilisateurs.
     * @param string $message      Le message de notification à envoyer.
     *
     * @return string Réponse de l'API OneSignal après l'envoi de la notification.
     *
     * @throws Exception Si une erreur survient lors de l'envoi de la notification.
     */
    function sendNotification($targets, $message) {
        $contents = array(
            'en' => $message
        );

        $headings = array(
            'en' => 'Alerte de votre établissement'
        );

        if (is_null($targets)) {
            $fields = array(
                'app_id' => ONESIGNAL_APP_ID,
                'included_segments' => array(
                    'All'
                ),
                'contents' => $contents,
                'headings' => $headings
            );
        } else {
            // Generate the filters from the ADE codes
            $filters = array();
            $targets_length = count($targets);

            for ($i = 0; $i < $targets_length - 1; ++$i) {
                array_push(
                    $filters,
                    array(
                        'field' => 'tag',
                        'key' => 'code:' . $targets[$i]->getCode(),
                        'relation' => 'exists'
                    ),
                    array(
                        'operator' => 'OR'
                    )
                );
            }

            array_push(
                $filters,
                array(
                    'field' => 'tag',
                    'key' => 'code:' . $targets[$targets_length - 1]->getCode(),
                    'relation' => 'exists'
                )
            );

            $fields = array(
                'app_id' => ONESIGNAL_APP_ID,
                'included_segments' => array(
                    'All'
                ),
                'contents' => $contents,
                'headings' => $headings,
                'filters' => $filters
            );
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . ONESIGNAL_API_KEY
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}