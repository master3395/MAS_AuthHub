<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Webauthn
{
    public static function rpId(CMSModule $mod)
    {
        $custom = trim((string) $mod->GetPreference('mas_ah_webauthn_rp_id', ''));
        if ($custom !== '') {
            return $custom;
        }
        $config = cms_utils::get_config();
        $host = parse_url((string) $config['root_url'], PHP_URL_HOST);
        return $host ? (string) $host : 'localhost';
    }

    public static function handleRequest(CMSModule $mod, array $params)
    {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!is_readable($autoload)) {
            return array('error' => 'webauthn_vendor_missing');
        }
        require_once $autoload;
        if (!class_exists('Webauthn\\PublicKeyCredentialRpEntity')) {
            return array('error' => 'webauthn_not_available');
        }
        $mode = isset($params['mode']) ? (string) $params['mode'] : 'login';
        $jsonIn = file_get_contents('php://input');
        $body = $jsonIn ? json_decode($jsonIn, true) : array();
        if (!is_array($body)) {
            $body = array();
        }
        if ($mode === 'login' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !empty($body['action'])) {
            return self::assertLogin($mod, $body, $params);
        }
        return array('error' => 'invalid_mode');
    }

    private static function assertLogin(CMSModule $mod, array $body, array $params)
    {
        $credId = isset($body['credential_id']) ? (string) $body['credential_id'] : '';
        if ($credId === '') {
            return array('error' => 'missing_credential');
        }
        $db = $mod->GetDb();
        $row = $db->GetRow(
            'SELECT * FROM ' . Mas_Ah_Tables::webauthnCredentials() . ' WHERE credential_id = ? LIMIT 1',
            array($credId)
        );
        if (!is_array($row)) {
            Mas_Ah_Audit::log($mod, 'webauthn.unknown_credential', 'warning', array(), 0, 0);
            return array('error' => 'unknown_credential');
        }
        $returnType = isset($params['return_type']) ? (string) $params['return_type'] : 'mams';
        if ($returnType === 'admin' && (int) $row['cms_user_id'] > 0) {
            $userops = UserOperations::get_instance();
            $user = $userops->LoadUserByID((int) $row['cms_user_id']);
            if ($user && Mas_Ah_Policy::cmsUserAllowed($mod, $user)) {
                Mas_Ah_CmsLoginBridge::loginCmsUser($user);
                return array('ok' => true, 'redirect' => 'admin');
            }
        }
        if ((int) $row['mams_user_id'] > 0) {
            Mas_Ah_MamsProvisioner::finalizeMamsLogin($mod, (int) $row['mams_user_id'], array('id' => 0, 'name' => 'webauthn', 'config' => array()), $credId);
            return array('ok' => true, 'redirect' => 'mams');
        }
        return array('error' => 'no_user');
    }

    public static function registerCredential(CMSModule $mod, $credentialId, $publicKey, $cmsUserId = 0, $mamsUserId = 0, $label = '')
    {
        if ($cmsUserId < 1 && $mamsUserId < 1) {
            return false;
        }
        $mod->GetDb()->Execute(
            'INSERT INTO ' . Mas_Ah_Tables::webauthnCredentials()
            . ' (credential_id, public_key, sign_count, aaguid, label, cms_user_id, mams_user_id, created) VALUES (?,?,0,\'\',?,?,?,?)',
            array(
                (string) $credentialId,
                (string) $publicKey,
                substr((string) $label, 0, 128),
                (int) $cmsUserId,
                (int) $mamsUserId,
                time(),
            )
        );
        return true;
    }
}
