<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_OauthServer
{
    public static function getClient(CMSModule $mod, $clientId)
    {
        $db = $mod->GetDb();
        return $db->GetRow(
            'SELECT * FROM ' . Mas_Ah_Tables::oauthClients() . ' WHERE client_id = ? AND enabled = 1 LIMIT 1',
            array((string) $clientId)
        );
    }

    public static function verifyClientSecret(array $client, $secret)
    {
        if (empty($client['secret_hash'])) {
            return false;
        }
        return password_verify((string) $secret, (string) $client['secret_hash']);
    }

    public static function createAuthCode(CMSModule $mod, array $data)
    {
        $code = bin2hex(random_bytes(24));
        $expires = time() + 300;
        $mod->GetDb()->Execute(
            'INSERT INTO ' . Mas_Ah_Tables::oauthAuthCodes()
            . ' (code, client_id, user_id, scopes, redirect_uri, expires_at, used, created)'
            . ' VALUES (?,?,?,?,?,?,0,?)',
            array(
                $code,
                (string) ($data['client_id'] ?? ''),
                (int) ($data['user_id'] ?? 0),
                (string) ($data['scopes'] ?? 'openid'),
                (string) ($data['redirect_uri'] ?? ''),
                $expires,
                time(),
            )
        );
        return $code;
    }

    public static function exchangeCode(CMSModule $mod, $code, $clientId, $redirectUri)
    {
        $db = $mod->GetDb();
        $row = $db->GetRow(
            'SELECT * FROM ' . Mas_Ah_Tables::oauthAuthCodes()
            . ' WHERE code = ? AND client_id = ? LIMIT 1',
            array((string) $code, (string) $clientId)
        );
        if (!is_array($row) || (int) $row['used'] === 1 || (int) $row['expires_at'] < time()) {
            return null;
        }
        if ($redirectUri !== '' && (string) $row['redirect_uri'] !== $redirectUri) {
            return null;
        }
        $db->Execute(
            'UPDATE ' . Mas_Ah_Tables::oauthAuthCodes() . ' SET used = 1 WHERE id = ?',
            array((int) $row['id'])
        );
        $access = bin2hex(random_bytes(32));
        $refresh = bin2hex(random_bytes(32));
        $uid = (int) $row['user_id'];
        Mas_Ah_TokenStore::store($mod, array(
            'token_type' => 'access',
            'token' => $access,
            'scopes' => (string) $row['scopes'],
            'expires_at' => time() + 3600,
            'provider_id' => 0,
            'cms_user_id' => $uid,
            'mams_user_id' => 0,
        ));
        Mas_Ah_TokenStore::store($mod, array(
            'token_type' => 'refresh',
            'token' => $refresh,
            'scopes' => (string) $row['scopes'],
            'expires_at' => time() + (int) $mod->GetPreference('mas_ah_refresh_ttl', '2592000'),
            'provider_id' => 0,
            'cms_user_id' => $uid,
            'mams_user_id' => 0,
        ));
        $idToken = Mas_Ah_Jwt::encode($mod, array(
            'sub' => (string) $uid,
            'scope' => (string) $row['scopes'],
        ));
        return array(
            'access_token' => $access,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'refresh_token' => $refresh,
            'id_token' => $idToken,
        );
    }
}
