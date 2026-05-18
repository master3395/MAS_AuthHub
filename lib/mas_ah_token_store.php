<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_TokenStore
{
    public static function store(CMSModule $mod, array $row)
    {
        $enc = Mas_Ah_Crypto::encrypt($mod, (string) ($row['token'] ?? ''));
        if ($enc === '') {
            return 0;
        }
        $db = $mod->GetDb();
        $db->Execute(
            'INSERT INTO ' . Mas_Ah_Tables::tokens()
            . ' (token_type, token_enc, scopes, expires_at, provider_id, cms_user_id, mams_user_id, created)'
            . ' VALUES (?,?,?,?,?,?,?,?)',
            array(
                substr((string) ($row['token_type'] ?? 'access'), 0, 16),
                $enc,
                substr((string) ($row['scopes'] ?? ''), 0, 255),
                (int) ($row['expires_at'] ?? 0),
                (int) ($row['provider_id'] ?? 0),
                (int) ($row['cms_user_id'] ?? 0),
                (int) ($row['mams_user_id'] ?? 0),
                time(),
            )
        );
        return (int) $db->Insert_ID();
    }

    public static function revoke(CMSModule $mod, $tokenId)
    {
        $mod->GetDb()->Execute(
            'DELETE FROM ' . Mas_Ah_Tables::tokens() . ' WHERE id = ?',
            array((int) $tokenId)
        );
    }

    public static function listForUser(CMSModule $mod, $cmsUserId = 0, $mamsUserId = 0, $limit = 50)
    {
        $limit = max(1, min(200, (int) $limit));
        $db = $mod->GetDb();
        if ($cmsUserId > 0) {
            $sql = 'SELECT id, token_type, scopes, expires_at, provider_id, created FROM '
                . Mas_Ah_Tables::tokens() . ' WHERE cms_user_id = ? ORDER BY id DESC LIMIT ' . $limit;
            return $db->GetArray($sql, array((int) $cmsUserId));
        }
        if ($mamsUserId > 0) {
            $sql = 'SELECT id, token_type, scopes, expires_at, provider_id, created FROM '
                . Mas_Ah_Tables::tokens() . ' WHERE mams_user_id = ? ORDER BY id DESC LIMIT ' . $limit;
            return $db->GetArray($sql, array((int) $mamsUserId));
        }
        $sql = 'SELECT id, token_type, scopes, expires_at, provider_id, cms_user_id, mams_user_id, created FROM '
            . Mas_Ah_Tables::tokens() . ' ORDER BY id DESC LIMIT ' . $limit;
        return $db->GetArray($sql);
    }
}
