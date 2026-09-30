<?php
declare(strict_types=1); session_start();
const ROOT = __DIR__ . '/..'; const CONFIG_FILE = ROOT . '/data/config.json'; const AUTH_FILE = ROOT . '/data/auth.json';
function json_out($data, int $code=200): never { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); echo json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); exit; }
function config(): array { $x=json_decode((string)@file_get_contents(CONFIG_FILE),true); return is_array($x)?$x:[]; }
function require_auth(): void { if(empty($_SESSION['dashboard_admin'])) json_out(['error'=>'Unauthorized'],401); }
function body(): array { $x=json_decode((string)file_get_contents('php://input'),true); return is_array($x)?$x:[]; }
function clean_rel(string $p): string { $p=str_replace('\\','/',$p); if(str_contains($p,'..')||str_starts_with($p,'/')) return ''; return $p; }
function save_config(array $cfg): void { $tmp=CONFIG_FILE.'.tmp'; $json=json_encode($cfg,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); if(file_put_contents($tmp,$json,LOCK_EX)===false||!rename($tmp,CONFIG_FILE)) json_out(['error'=>'Cannot write data/config.json. Grant the web user write access.'],500); }
