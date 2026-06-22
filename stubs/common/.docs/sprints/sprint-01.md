# Sprint {SPRINT_PADDED} — {SPRINT_TITLE}

**Status**: active
**Goal**: {SPRINT_TITLE}
**ETC**: {SPRINT_ETC}
**PIC**: {SPRINT_PIC}
**Team**: {TEAM_NAME}

---

## Briefs

### Brief {SPRINT_PADDED}-01 — Project scaffold validation

**Objective**: Confirm the starter kit installed correctly and the toolchain works.

**Acceptance criteria**:
- [ ] `php artisan module:make Auth` runs without error
- [ ] Backend: `Modules/Auth/` exists with full folder structure
- [ ] Frontend: `resources/js/modules/auth/` exists with service, store, view, routes
- [ ] `composer dump-autoload` completes without error
- [ ] `php artisan serve` starts on port 8000

---

### Brief {SPRINT_PADDED}-02 — Authentication module

**Objective**: Implement login/logout with Keycloak (Sanctum fallback).

**Acceptance criteria**:
- [ ] `POST /api/auth/login` returns `{ data: { token }, message }`
- [ ] `POST /api/auth/logout` invalidates the session
- [ ] `GET /api/auth/me` returns the authenticated user
- [ ] Unauthenticated requests return 401
- [ ] Frontend `authStore` stores and clears the token
- [ ] Login page redirects to dashboard on success

**Files expected**:
- `Modules/Auth/app/Http/Controllers/AuthController.php`
- `Modules/Auth/app/Http/Requests/LoginRequest.php`
- `Modules/Auth/app/Services/AuthService.php`
- `resources/js/modules/auth/services/authService.js`
- `resources/js/modules/auth/stores/authStore.js`
- `resources/js/modules/auth/views/LoginView.vue`

---

## Sprint Checklist

- [ ] Brief {SPRINT_PADDED}-01 complete
- [ ] Brief {SPRINT_PADDED}-02 complete
- [ ] All tests pass (`php artisan test`)
- [ ] No lint errors (`npm run lint`)
- [ ] Sprint doc committed and archived

## Notes

<!-- Record decisions, blockers, or scope changes here -->
