<?php

namespace Utils;

include __DIR__ . '/../../config-notifs.php';

use Models\CodeAde;

/**
 * Class OneSignalPush
 *
 * Représente un service de notification push utilisant l'API OneSignal.
 * Cette classe permet d'envoyer des notifications à des utilisateurs en ciblant
 * des codes ADE spécifiques ou en envoyant à tous les utilisateurs.
 *
 * @package Utils
 */
class OneSignalPush
{
    /**
     * OneSignalPush constructor.
     *
     * Initialise une nouvelle instance de la classe OneSignalPush.
     */
    function __construct() {
        // Initialisation, si nécessaire
    }

    /**
     * Envoie une notification push à l'aide de OneSignal.
     *
     * @param CodeAde[]|null $targets Les codes ADE à cibler pour l'envoi de notifications,
     *                                 ou null pour cibler tous les utilisateurs.
     * @param string $message Le message de notification à envoyer.
     * @return string La réponse de la requête à OneSignal.
     *
     * @throws Exception Si l'envoi échoue pour une raison quelconque.
     *
     * @example
     * ```php
     * $push = new OneSignalPush();
     * $codeA = new CodeAde();
     * $codeA->setCode('CODE123');
     * $response = $push->sendNotification([$codeA], 'Ceci est un message de test.');
     * ```
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
            // Générer les filtres à partir des codes ADE
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

        return $response; // Retourne la réponse de l'API OneSignal
    }
}