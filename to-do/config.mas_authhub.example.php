<?php
/**
 * Example snippet for site config.php (do not include this file directly).
 */
return array(
    'mas_authhub' => array(
        'encryption_key' => 'replace-with-32-byte-random-secret',
        'signing_key' => 'replace-with-jwt-hs256-secret',
        'providers' => array(
            'azure_ad' => array(
                'client_secret' => 'from-azure-portal',
            ),
        ),
    ),
);
