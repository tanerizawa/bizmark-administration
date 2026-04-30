# Laravel Code Quality Audit Report (2026-04-19)

## Scope
- Full Laravel test baseline (Unit + Feature + full suite).
- E2E/integration smoke tests with Playwright.
- PSR-12/style conformance check and targeted refactor.
- Dormant/orphan view detection and cleanup (verified references only).
- Filesystem permission audit for `storage`, `bootstrap/cache`, and `vendor`.
- Laravel log review in `storage/logs/laravel.log`.

## Executed Validation
- `php artisan test --testsuite=Unit` -> passed.
- `php artisan test --testsuite=Feature` -> passed.
- `php artisan test` -> **93 passed**.
- `npx playwright test --reporter=line --workers=1` -> passed after selector fixes.
- `npm run build` -> passed.
- `php artisan route:list` -> passed.
- `php artisan view:cache` -> passed.
- `composer dump-autoload -o` -> passed.

## Refactor & Fixes Applied

### 1) E2E test stability fixes
- Updated brittle/ambiguous selectors:
  - `tests/e2e/admin-login-permits.spec.ts`
  - `tests/e2e/client-login.spec.ts`
- Replaced ambiguous `getByLabel('Password')` and role-name login button query with deterministic selectors:
  - `input[name="password"]`
  - `button[type="submit"]`

### 2) Seeder made idempotent for repeatable integration runs
- File: `database/seeders/E2ESeeder.php`
- Changes:
  - `updateOrCreate` for `PermitType`, `Client`, and `User`.
  - Added required fields to satisfy schema constraints (`category`, admin `name`).
- Impact:
  - Seeder can be re-run safely during CI/local integration cycles.

### 3) Cache failure hardening for landing controller
- File: `app/Http/Controllers/PublicArticleController.php`
- Changes:
  - Wrapped landing article cache access in `try/catch`.
  - Added graceful fallback DB query when cache store is unavailable.
  - Added warning log (non-fatal) instead of request-breaking behavior.

### 4) Style conformance (PSR-12) on touched backend files
- Command run:
  - `./vendor/bin/pint app/Http/Controllers/PublicArticleController.php database/seeders/E2ESeeder.php routes/mobile.php routes/web.php app/Http/Controllers/ServiceController.php`
- Result:
  - style issues auto-fixed on those files.

### 5) Dormant/orphan cleanup (verified)
- Removed legacy views no longer reachable from active routes/controllers:
  - `resources/views/services/mobile-index.blade.php`
  - `resources/views/services/mobile-show.blade.php`
  - `resources/views/legal/mobile-privacy.blade.php`
  - `resources/views/legal/mobile-terms.blade.php`
  - `resources/views/mobile-landing/` (entire folder)
- Verification:
  - grep checks confirmed no active references remain.

## Permission Audit Results

### Observed
- Real permission/ownership mismatch exists in this environment:
  - Many project files owned by `nobody:nogroup` with restrictive write behavior for current execution user.
- Attempted runtime fixes (`chmod/chown`) on target directories returned:
  - `Operation not permitted`

### Impact
- This is an environment-level filesystem ownership policy issue, not purely app-code issue.
- It can cause intermittent `Permission denied` behavior depending on runtime user.

### Required Ops Remediation (host-level)
- Run these on host (with proper privileges) in deployment/local machine:

```bash
# set app owner (example: www-data)
sudo chown -R www-data:www-data /path/to/project

# enforce folder/file modes
sudo find /path/to/project/storage /path/to/project/bootstrap/cache /path/to/project/vendor -type d -exec chmod 755 {} \;
sudo find /path/to/project/storage /path/to/project/bootstrap/cache /path/to/project/vendor -type f -exec chmod 644 {} \;

# ensure writable runtime dirs
sudo chmod -R u+rwX,g+rX /path/to/project/storage /path/to/project/bootstrap/cache
```

> Notes:
> - For dev-only environments, owner/group can be your local user.
> - In production, align owner/group with PHP-FPM/Nginx runtime user.

## Log Review Summary (`storage/logs/laravel.log`)
- Historical exceptions were found (including cache write errors).
- Relevant hardening applied in `PublicArticleController` to prevent cache store failure from impacting page response.
- No new failing assertions introduced after fixes; tests and build remain green.

## Final Status
- Test status: **green** (unit + feature + full + E2E smoke).
- Build status: **green**.
- Refactor status: targeted quality improvements applied and formatted.
- Dormant view cleanup: completed for verified unreachable assets.
- Permission issue: **identified precisely**; host-level remediation commands provided (required to achieve guaranteed zero permission incidents across environments).
