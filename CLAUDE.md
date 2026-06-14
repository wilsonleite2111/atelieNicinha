# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

```bash
# Start development (PHP + Vite + queue + logs)
composer run dev

# Run all tests
php artisan test --compact

# Run a single test file
php artisan test --compact tests/Feature/Teams/TeamTest.php

# Run tests matching a name
php artisan test --compact --filter=testName

# Lint & format PHP (run after any PHP change)
vendor/bin/pint --dirty --format agent

# Regenerate Wayfinder TypeScript route functions
php artisan wayfinder:generate

# Build frontend assets
npm run build
```

## Architecture

**atélieNicinha** is a team-centric multi-tenant SPA built on Laravel 13 + Inertia.js v3 + Vue 3.

### Team-Based Routing

Every authenticated route is team-scoped via `/{current_team}/dashboard`. `SetTeamUrlDefaults` middleware injects the team slug into all named route generations. `EnsureTeamMembership` guards protected routes to confirm the authenticated user belongs to the current team.

### Authentication Flow

Fortify handles all auth: login → optional 2FA → email verification → redirect to team dashboard. Custom response contracts in `app/Responses/` override Fortify's default redirects to respect the team URL structure.

### Shared Inertia Props

`HandleInertiaRequests` shares auth user (with current team & roles), flash messages, and available teams to every Inertia page. Flash messages flow through `lib/flashToast.ts` → Vue Sonner.

### Key Model Relationships

- `User` → `hasManyThrough` teams via `team_members` pivot (role column), plus `current_team_id`
- `Team` → soft-deletes, auto-generates unique slug via `GeneratesUniqueTeamSlugs` trait
- `Membership` → pivot model with `TeamRole` enum (Owner, Member)
- `TeamInvitation` → stores invitation code + expiry, triggers `TeamInvitation` notification email

### UI & Media

- Reka-UI headless primitives; styled wrappers in `resources/js/components/ui/`. Check existing components before creating new ones.
- File uploads via Spatie Media Library — add `HasMedia` + `InteractsWithMedia` to models.
