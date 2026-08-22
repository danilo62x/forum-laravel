<?php

namespace App\Support;

/**
 * Autenticação fictícia (espelha forum/react stores/auth.ts).
 *
 * Qualquer e-mail/senha não vazios entram. A conta sugerida na tela de login
 * é moderador@agora.com / demo123, ligada ao membro 1 do fórum (Marina Duarte).
 */
class MockUsers
{
    public const DEMO_EMAIL = 'moderador@agora.com';

    public const DEMO_PASSWORD = 'demo123';

    /**
     * @return array{id:int,name:string,email:string,role:string,organization:string,memberId:int}|null
     */
    public function authenticate(string $email, string $password): ?array
    {
        if ($email === '' || $password === '') {
            return null;
        }

        $isDemo = $email === self::DEMO_EMAIL;
        $handle = explode('@', $email)[0] ?: 'Moderador';
        $raw = trim((string) preg_replace('/[._-]/', ' ', $handle)) ?: 'Moderador';
        $name = $isDemo ? 'Marina Duarte' : mb_strtoupper(mb_substr($raw, 0, 1)).mb_substr($raw, 1);

        return [
            'id' => 1,
            'name' => $name,
            'email' => $email,
            'role' => 'Moderadora da comunidade',
            'organization' => 'Ágora',
            // Liga a conta ao perfil público do membro 1.
            'memberId' => 1,
        ];
    }

    /** @return array<string,mixed> */
    public function register(string $name, string $email): array
    {
        return [
            'id' => 1,
            'name' => $name,
            'email' => $email,
            'role' => 'Membro',
            'organization' => '',
            'memberId' => 1,
        ];
    }
}
