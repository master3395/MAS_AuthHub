<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Session
{
    public static function startHubSession(CMSModule $mod, array $data)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $sid = bin2hex(random_bytes(24));
        $expires = time() + (int) $mod->GetPreference('mas_ah_session_ttl', '3600');
        $ip = function_exists('cms_utils::get_real_ip') ? cms_utils::get_real_ip() : '';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
        $db = $mod->GetDb();
        $db->Execute(
            'INSERT INTO ' . Mas_Ah_Tables::sessions()
            . ' (session_id, cms_user_id, mams_user_id, provider_id, ip_hash, ua_hash, expires_at, revoked, created)'
            . ' VALUES (?,?,?,?,?,?,?,0,?)',
            array(
                $sid,
                (int) ($data['cms_user_id'] ?? 0),
                (int) ($data['mams_user_id'] ?? 0),
                (int) ($data['provider_id'] ?? 0),
                Mas_Ah_Crypto::hashIp($ip),
                hash('sha256', $ua),
                $expires,
                time(),
            )
        );
        $_SESSION['mas_ah_sid'] = $sid;
        return $sid;
    }

    public static function revoke(CMSModule $mod, $sessionId)
    {
        $mod->GetDb()->Execute(
            'UPDATE ' . Mas_Ah_Tables::sessions() . ' SET revoked = 1 WHERE session_id = ?',
            array((string) $sessionId)
        );
        if (isset($_SESSION['mas_ah_sid']) && $_SESSION['mas_ah_sid'] === $sessionId) {
            unset($_SESSION['mas_ah_sid']);
        }
    }

    public static function revokeAllForUser(CMSModule $mod, $cmsUserId = 0, $mamsUserId = 0)
    {
        if ($cmsUserId > 0) {
            $mod->GetDb()->Execute(
                'UPDATE ' . Mas_Ah_Tables::sessions() . ' SET revoked = 1 WHERE cms_user_id = ?',
                array((int) $cmsUserId)
            );
        }
        if ($mamsUserId > 0) {
            $mod->GetDb()->Execute(
                'UPDATE ' . Mas_Ah_Tables::sessions() . ' SET revoked = 1 WHERE mams_user_id = ?',
                array((int) $mamsUserId)
            );
        }
    }

    public static function listActive(CMSModule $mod, $limit = 100)
    {
        $limit = max(1, min(500, (int) $limit));
        $now = time();
        $sql = 'SELECT * FROM ' . Mas_Ah_Tables::sessions()
            . ' WHERE revoked = 0 AND expires_at > ' . (int) $now
            . ' ORDER BY created DESC LIMIT ' . $limit;
        return $mod->GetDb()->GetArray($sql);
    }

    public static function clearOAuthState()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        unset(
            $_SESSION['mas_ah_oauth_state'],
            $_SESSION['mas_ah_oauth_pkce'],
            $_SESSION['mas_ah_oauth_provider'],
            $_SESSION['mas_ah_return_type']
        );
    }
}
