<?php

/**
 * ZodPanel Webmail SSO Plugin for Roundcube
 * Authenticates via Dovecot Master User with HMAC-SHA256 signed tokens
 */
class zodpanel_sso extends rcube_plugin
{
    public function init()
    {
        $this->add_hook('startup', [$this, 'startup']);
        $this->add_hook('authenticate', [$this, 'authenticate']);
        $this->add_hook('login_after', [$this, 'login_after']);
        $this->add_hook('user_create', [$this, 'user_create']);
    }

    public function startup($args)
    {
        if (!empty($_GET['_zod_sso']) && empty($_SESSION['user_id'])) {
            $args['task'] = 'login';
            $args['action'] = 'login';
        }
        return $args;
    }

    public function authenticate($args)
    {
        if (!empty($_GET['_zod_sso'])) {
            $user = trim((string) ($_GET['user'] ?? ''));
            $time = intval($_GET['t'] ?? 0);
            $token = trim((string) ($_GET['s'] ?? ''));

            $env = $this->load_env();
            $masterUser = $env['WEBMAIL_SSO_MASTER_USER'] ?? '';
            $masterPass = $env['WEBMAIL_SSO_MASTER_PASS'] ?? '';

            if (empty($user) || empty($token) || empty($masterUser) || empty($masterPass)) {
                return $args;
            }

            if (abs(time() - $time) > 600) {
                return $args;
            }

            $expectedToken = hash_hmac('sha256', $user . '|' . $time, $masterPass);
            if (!hash_equals($expectedToken, $token)) {
                return $args;
            }

            $args['user'] = $user . '*' . $masterUser;
            $args['pass'] = $masterPass;
            $args['host'] = 'localhost:143';
            $args['cookiecheck'] = false;
            $args['valid'] = true;
        }

        return $args;
    }

    public function login_after($args)
    {
        if (!empty($_GET['_zod_sso'])) {
            return ['_task' => 'mail'];
        }
        return $args;
    }

    public function user_create($args)
    {
        if (strpos($args['user'], '*') !== false) {
            $cleanUser = preg_replace('/\*.*$/', '', $args['user']);
            $args['user_email'] = $cleanUser;
        }
        return $args;
    }

    private function load_env(): array
    {
        $path = '/etc/whmpanel/webmail-sso.env';
        if (!is_readable($path)) {
            return [];
        }

        $values = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $values[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $values;
    }
}