<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_UserLink
{
    public static function findByExternal(CMSModule $mod, $providerId, $externalSub)
    {
        $db = $mod->GetDb();
        $sql = 'SELECT * FROM ' . Mas_Ah_Tables::userLinks()
            . ' WHERE provider_id = ? AND external_sub = ? LIMIT 1';
        $row = $db->GetRow($sql, array((int) $providerId, (string) $externalSub));
        return is_array($row) ? $row : null;
    }

    public static function findByEmail(CMSModule $mod, $email, $providerId = 0)
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return null;
        }
        $db = $mod->GetDb();
        if ($providerId > 0) {
            $sql = 'SELECT * FROM ' . Mas_Ah_Tables::userLinks() . ' WHERE email = ? AND provider_id = ? LIMIT 1';
            $row = $db->GetRow($sql, array($email, (int) $providerId));
        } else {
            $sql = 'SELECT * FROM ' . Mas_Ah_Tables::userLinks() . ' WHERE email = ? LIMIT 1';
            $row = $db->GetRow($sql, array($email));
        }
        return is_array($row) ? $row : null;
    }

    public static function upsert(CMSModule $mod, array $data)
    {
        $providerId = (int) ($data['provider_id'] ?? 0);
        $externalSub = (string) ($data['external_sub'] ?? '');
        $cmsUserId = isset($data['cms_user_id']) ? (int) $data['cms_user_id'] : null;
        $mamsUserId = isset($data['mams_user_id']) ? (int) $data['mams_user_id'] : null;
        if ($cmsUserId === null && $mamsUserId === null) {
            return false;
        }
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $attrs = isset($data['attrs_json']) ? $data['attrs_json'] : array();
        if (!is_string($attrs)) {
            $attrs = json_encode($attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $existing = self::findByExternal($mod, $providerId, $externalSub);
        $db = $mod->GetDb();
        $now = time();
        if ($existing) {
            $sql = 'UPDATE ' . Mas_Ah_Tables::userLinks()
                . ' SET cms_user_id = COALESCE(?, cms_user_id), mams_user_id = COALESCE(?, mams_user_id),'
                . ' email = ?, attrs_json = ?, updated = ? WHERE id = ?';
            $db->Execute($sql, array(
                $cmsUserId,
                $mamsUserId,
                $email,
                $attrs,
                $now,
                (int) $existing['id'],
            ));
            return (int) $existing['id'];
        }
        $sql = 'INSERT INTO ' . Mas_Ah_Tables::userLinks()
            . ' (provider_id, external_sub, cms_user_id, mams_user_id, email, attrs_json, created, updated)'
            . ' VALUES (?,?,?,?,?,?,?,?)';
        $db->Execute($sql, array(
            $providerId,
            $externalSub,
            $cmsUserId,
            $mamsUserId,
            $email,
            $attrs,
            $now,
            $now,
        ));
        return (int) $db->Insert_ID();
    }

    public static function listRecent(CMSModule $mod, $limit = 50)
    {
        $limit = max(1, min(500, (int) $limit));
        $db = $mod->GetDb();
        $sql = 'SELECT ul.*, p.name AS provider_name FROM ' . Mas_Ah_Tables::userLinks() . ' ul'
            . ' LEFT JOIN ' . Mas_Ah_Tables::providers() . ' p ON p.id = ul.provider_id'
            . ' ORDER BY ul.updated DESC LIMIT ' . $limit;
        return $db->GetArray($sql);
    }
}
