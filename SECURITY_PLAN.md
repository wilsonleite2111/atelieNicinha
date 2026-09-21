# Plano de Implementação — Segurança

Análise realizada em **2026-09-20** sobre a branch `modulo-admin`. Achados verificados manualmente nos arquivos citados.

---

## 🔴 CRÍTICO

### 1. IDOR na FK `category_id` (multi-tenancy quebrada)

**Local:** `app/Http/Requests/Products/SaveProductRequest.php:35`

**Problema:** A regra só verifica se a categoria existe globalmente:
```php
'category_id' => ['nullable', 'integer', 'exists:categories,id'],
```
Um admin do time A pode enviar `category_id` de uma categoria do time B. O `ProductController` aceita, criando um produto com FK cross-tenant.

**Fix:**
```php
use Illuminate\Validation\Rule;

'category_id' => [
    'nullable',
    'integer',
    Rule::exists('categories', 'id')->where('team_id', $this->route('current_team')->id),
],
```

**Esforço:** 5 min

---

### 2. `Log::error` serializa exception completa

**Local:** `app/Http/Controllers/Products/ProductController.php:74, 132`

**Problema:**
```php
Log::error('Product store failed', ['exception' => $e]);
```
Serializa stack trace + eventualmente request payload (uploads, tokens de sessão). Se logs forem enviados a agregadores externos (Sentry, Papertrail), dados vazam para fora do perímetro.

**Fix:**
```php
Log::error('Product store failed', [
    'message' => $e->getMessage(),
    'team_id' => $current_team->id,
    'user_id' => $request->user()->id,
]);
```

**Esforço:** 2 min

---

## 🟠 ALTA

### 3. Ausência de headers de segurança

**Local:** `bootstrap/app.php:17-25`

**Problema:** Nenhum middleware adiciona **HSTS**, **CSP**, **X-Content-Type-Options** ou **Referrer-Policy**. Em produção, app fica exposta a clickjacking, MIME sniffing e downgrade HTTPS.

**Fix:** Criar `app/Http/Middleware/SecurityHeaders.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
```

Registrar em `bootstrap/app.php`:
```php
$middleware->web(append: [
    HandleAppearance::class,
    HandleInertiaRequests::class,
    AddLinkHeadersForPreloadedAssets::class,
    SetTeamUrlDefaults::class,
    SecurityHeaders::class, // ← adicionar
]);
```

**CSP:** deixar para depois — requer análise dos assets do Vite e origens externas (Google Fonts, WhatsApp).

**Esforço:** 30 min

---

### 4. Rate limiting frágil no login

**Local:** `app/Providers/FortifyServiceProvider.php` (verificar)

**Problema:** Fortify default é 5 tentativas/min sem lockout progressivo. Atacante pode fazer credential stuffing a ~4.999 tentativas/dia por conta sem ser bloqueado.

**Fix:** Custom throttling com backoff:
```php
RateLimiter::for('login', function (Request $request) {
    $email = (string) $request->email;
    $key = Str::transliterate(Str::lower($email).'|'.$request->ip());

    return [
        Limit::perMinute(5)->by($key),
        Limit::perHour(20)->by($key),
        Limit::perDay(50)->by($request->ip()),
    ];
});
```

**Esforço:** 20 min

---

### 5. Mass assignment em `team_id`

**Locais:** `app/Models/Product.php:14`, `app/Models/Category.php:11`

**Problema:** `team_id` está em `#[Fillable]`. Está **mitigado hoje** pelo uso de `$current_team->products()->create(...)`, mas viola defesa em profundidade — qualquer refactor futuro para `Product::create($request->validated())` vira privilege escalation.

**Fix:** Remover `team_id` de ambos os fillables:
```php
// Product.php
#[Fillable(['category_id', 'name', 'description', 'size', 'price', 'active'])]

// Category.php
#[Fillable(['name', 'active'])]
```

A criação continua funcionando via `$team->products()->create(...)` e `$team->categories()->create(...)`, que injetam a FK automaticamente.

**Esforço:** 2 min + rodar testes

---

### 6. `SESSION_ENCRYPT=false` e `SESSION_SECURE_COOKIE` sem default

**Locais:** `.env.example:32`, `config/session.php:172`

**Problema:** Cookies de sessão não criptografados; flag `Secure` só aplicada se explicitamente setada. Em prod HTTPS funciona, mas se HTTP escapar (redirect esquecido, proxy misconfig), sessão vai em claro.

**Fix:** Adicionar ao checklist de deploy (não é mudança de código):
```
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

**Esforço:** documentação apenas

---

## 🟡 MÉDIA

### 7. Falta `authorize()` em alguns FormRequests

**Locais:** requests fora do escopo desta análise (ex: `CreateTeamInvitationRequest`)

**Problema:** Alguns requests delegam autorização inteiramente ao controller. Se algum controller esquecer `Gate::authorize`, o request passa livre.

**Fix:** Auditar todos os FormRequests em `app/Http/Requests/**` e implementar `authorize()` explicitamente em cada um.

**Esforço:** 30 min (audit completo)

---

### 8. Timeout de password confirmation muito longo

**Local:** `config/auth.php` (setting `password_timeout`)

**Problema:** Padrão Fortify é 10800s (3h). Para operações destrutivas (deletar conta, desabilitar 2FA), 15 min é o razoável.

**Fix:**
```php
'password_timeout' => 900, // 15 minutos
```

**Esforço:** 1 min

---

### 9. `HandleInertiaRequests` expõe user inteiro

**Local:** `app/Http/Middleware/HandleInertiaRequests.php:44`

**Problema:** `'user' => $user` serializa tudo. Está **parcialmente protegido** pelo `#[Hidden]` no User model (senha, 2FA secret, recovery codes), mas ainda vaza `email`, `email_verified_at`, `current_team_id`, `active`. Nem tudo é secret, mas o princípio de menor exposição pede DTO explícito.

**Fix:**
```php
'auth' => [
    'user' => $user ? [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'email_verified_at' => $user->email_verified_at,
    ] : null,
],
```

**Esforço:** 10 min + verificar consumo no frontend

---

### 10. Sem verificação de duplicidade em convites de time

**Local:** `app/Http/Controllers/Teams/TeamInvitationController.php` (verificar)

**Problema:** Nada impede enviar N convites para o mesmo email → email flooding via a app.

**Fix:** Antes de criar o convite, validar:
```php
$exists = $team->invitations()
    ->where('email', $request->email)
    ->where('expires_at', '>', now())
    ->exists();

if ($exists) {
    throw ValidationException::withMessages([
        'email' => 'Já existe um convite ativo para este email.',
    ]);
}
```

Adicionar também throttle na rota: `->middleware('throttle:5,10')`.

**Esforço:** 15 min

---

## 🟢 BAIXA / Observações

### 11. WhatsApp hardcoded

**Locais:** `resources/js/pages/Welcome.vue:359, 440`

**Fix:** Mover para `config/services.php` e passar via shared props do Inertia.

**Esforço:** 15 min

---

### 12. `CategoryPolicy` sem método `view()`

**Local:** `app/Policies/CategoryPolicy.php`

**Problema:** Sem impacto atual (não há rota pública), mas policy incompleta.

**Fix:** Adicionar `view()` se e quando surgir rota de visualização individual.

---

### 13. `ProductPolicy::view()` existe mas nenhuma rota o usa

**Local:** `app/Policies/ProductPolicy.php:17`

**Fix:** Remover dead code ou implementar a rota que o usa.

---

### 14. `APP_DEBUG=true` no `.env` local

Só afeta ambiente local (verificado). Atenção ao provisionamento de produção — garantir `APP_DEBUG=false` e `APP_ENV=production`.

---

## ✅ O que já está bem feito

- `EnsureTeamMembership` valida escopo em todas as rotas team-scoped.
- Controllers usam `abort_unless($model->team_id === $current_team->id, 404)` + `Gate::authorize`.
- Validação de upload restrita a `jpeg/png/webp` (bloqueia SVG-XSS).
- `#[Hidden]` no User model protege senha, 2FA secret e recovery codes.
- CSRF ativo por padrão, cookies encriptados (`encryptCookies`), `SameSite=lax`, `HttpOnly=true`.
- `password => hashed` cast + `Password::default()`.
- 2FA com `confirmPassword: true` habilitado.
- Composer sem wildcards; Laravel 13 e Fortify recentes.
- Uso consistente de `$request->validated()` / `$request->safe()` — sem `$request->all()`.

---

## Ordem sugerida de execução

| # | Item | Severidade | Esforço |
|---|------|------------|---------|
| 1 | IDOR em `category_id` | 🔴 Crítico | 5 min |
| 2 | Sanitizar `Log::error` | 🔴 Crítico | 2 min |
| 3 | Remover `team_id` de fillables | 🟠 Alta | 2 min |
| 4 | Timeout password confirmation | 🟡 Média | 1 min |
| 5 | Middleware de security headers | 🟠 Alta | 30 min |
| 6 | Rate limiting customizado | 🟠 Alta | 20 min |
| 7 | Duplicidade de convites | 🟡 Média | 15 min |
| 8 | DTO no `HandleInertiaRequests` | 🟡 Média | 10 min |
| 9 | WhatsApp em config | 🟢 Baixa | 15 min |
| 10 | Checklist de deploy `.env` prod | Doc | — |

**Total estimado:** ~1h40 para todos os itens de código.
