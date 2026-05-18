<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Audit
{
    public static function log(CMSModule $mod, $event, $severity, array $context = array(), $userId = 0, $providerId = 0)
    {
        $severity = in_array($severity, array('info', 'warning', 'error'), true) ? $severity : 'info';
        $event = substr(preg_replace('/[^a-z0-9_\.\:]/i', '', (string) $event), 0, 128);
        $json = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{}';
        }
        $ip = '';
        if (function_exists('cms_utils::get_real_ip')) {
            $ip = (string) cms_utils::get_real_ip();
        }
        $db = $mod->GetDb();
        $sql = 'INSERT INTO ' . Mas_Ah_Tables::auditLog()
            . ' (created, event, severity, user_id, provider_id, ip_hash, context_json)'
            . ' VALUES (?,?,?,?,?,?,?)';
        $db->Execute($sql, array(
            time(),
            $event,
            $severity,
            (int) $userId,
            (int) $providerId,
            Mas_Ah_Crypto::hashIp($ip),
            $json,
        ));
    }
}
