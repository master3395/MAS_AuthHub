<?php
if (!function_exists('cmsms')) {
    exit;
}
header('Content-Type: application/json; charset=utf-8');
$doc = Mas_Ah_OidcDiscovery::document($this);
echo json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
