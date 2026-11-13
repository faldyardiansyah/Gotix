# AGENTS.md — GOTIX Development Governance

> Permanent governance and engineering rules for all AI coding agents working on the **GOTIX** repository.  
> **Phase Baseline:** Established after Phase 1 Technical Audit and Phase 2 Validation.

---

## 1. Project Understanding

### Purpose
**GOTIX** is a web-based inter-city travel ticket reservation platform targeting routes across **Lampung Province** (`KotaSeeder`), supporting online seat booking, manual bank transfer payment verification, e-ticket issuance, walk-in counter reservations, and passenger complaints.

### Actual Technology Stack
- **Backend:** PHP `^8.2` (`8.2.12`), Laravel Framework `^12.0` (`v12.70.2`), MySQL (`database.default = mysql`).
- **Authentication:** Custom manual session authentication (`Auth::attempt()`, `Hash::make()`, `App\Models\Pengguna` on table `pengguna` — no Breeze/Fortify/Jetstream).
- **Payment Model:** Manual bank transfer (`config/gotix.php`) verified by Operator or Admin (no payment gateway such as Midtrans).
- **Frontend:** Blade templates, **Tailwind CSS v3** (`tailwindcss` via `postcss.config.js` & `tailwind.config.js` — note `@tailwindcss/vite` is also present in `package.json` but unused by Vite), `@tailwindcss/forms`, Alpine.js, and Vite.
- **Testing & QA:** Pest PHP (`^3.8`), Larastan/PHPStan (installed, `phpstan.neon` currently unconfigured), Laravel Pint (`^1.24`).

### Architecture & User Roles
- **Current Architectural State:** Route-grouped MVC (`routes/web.php`) using Eloquent models, PHP backed enums (`App\Enums\*`), custom role middleware (`PastikanPunyaPeran` aliased as `peran`), and logic currently placed directly in controllers (target remediation moves shared transactional logic into focused services/actions incrementally when approved).
- **Primary User Roles (`App\Enums\PeranPengguna`):**
  1. **Customer / Member (`Member`):** Prefix `/akun`, routes `member.*` — search trips, book seats, upload payment proof, view e-tickets, submit/view complaints.
  2. **Operator Loket (`Operator`):** Prefix `/operator`, routes `operator.*` — review and verify/reject customer payments, create walk-in counter orders.
  3. **Admin (`Admin`):** Prefix `/admin`, routes `admin.*` — dashboard metrics, manage Kota/Kendaraan/Jadwal, monitor users, verify/reject orders, reply to complaints.
  - **Public / Guest:** `/` (`beranda`), `/cari-jadwal` (`jadwal.cari`), `/masuk`, `/daftar`.

### Existing Directory Structure
```text
app/
├── Enums/                    # JenisKelamin, KategoriKeluhan, PeranPengguna, StatusPesanan
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            # DasborController, JadwalController, KeluhanController, KendaraanController, KotaController, PenggunaController, PesananController
│   │   ├── Auth/             # DaftarController, KeluarController, MasukController
│   │   ├── Member/           # KeluhanController, PembayaranController, PesananController, TiketController
│   │   ├── Operator/         # PesananLoketController, VerifikasiPembayaranController
│   │   └── Publik/           # BerandaController, CariJadwalController
│   ├── Middleware/           # PastikanPunyaPeran.php
│   └── Requests/Auth/        # DaftarRequest.php, MasukRequest.php
├── Models/                   # Jadwal, Keluhan, Kendaraan, KonfirmasiPembayaran, Kota, Pengguna, Pesanan
└── Providers/                # AppServiceProvider.php
config/                       # Includes config/gotix.php
database/                     # migrations/, seeders/, factories/
lang/id/                      # Indonesian localization (auth, pagination, passwords, validation)
resources/
├── css/app.css               # Tailwind v3 entry + DM Sans / Bricolage Grotesque
├── js/                       # app.js (Alpine.js), bootstrap.js (Axios)
└── views/                    # layouts/, publik/, auth/, member/, operator/, admin/
routes/                       # web.php, console.php
tests/                        # Pest.php, Feature/, Unit/
docs/                         # Phase 1 & Phase 2 audit, remediation, and design system docs
```

---

## 2. Source of Truth (Strict Priority Order)

When resolving ambiguity or contradictions, always follow this exact precedence:

1. **Actual source code and database migrations** (`app/`, `routes/`, `config/`, `database/migrations/`, `resources/`).
2. **Verified project documentation** (`docs/` audit and planning records).
3. **Approved business requirements** confirmed by the project owner.
4. **Explicit instructions from the project owner** for the current task.

**Mandatory Rule:** Never treat outdated design notes, speculative AI comments, or unverified assumptions as implemented behavior. Always verify directly against files on disk.

---

## 3. Development Rules

- **Inspect before modifying:** Always read relevant controllers, models, migrations, FormRequests, routes, and views before proposing or editing code.
- **Reuse existing components and functions:** Prefer existing enums, model relationships, `config/gotix.php` keys, and shared helpers over re-implementing logic.
- **Avoid duplicate implementations:** Consolidate repeated business logic (e.g., seat availability checks, payment verification transitions) rather than copying code across Member/Operator/Admin controllers.
- **Keep controllers focused:** Avoid embedding complex multi-table state changes, duplicate queries, or raw file-path handling directly in views or bloated controller methods.
- **Follow existing naming conventions:** Use Indonesian names for domain classes, tables, columns, routes, variables, and UI copy, keeping standard Laravel lifecycle methods (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) and architectural suffixes (`Controller`, `Request`, `Policy`, `Service`, `Action`).
- **Use validation consistently:** Align backend validation rules strictly with database column types, lengths, enum cases, and uniqueness constraints.
- **Maintain authorization on backend routes:** Always enforce `auth` + `peran:...` middleware plus resource-level ownership/state checks on backend endpoints.
- **Never trust frontend-only permission checks:** Hiding buttons or disabling inputs in Blade/Alpine is never sufficient security.
- **Avoid unnecessary dependencies:** Do not install Composer or npm packages without explicit justification and owner approval.
- **Avoid unnecessary abstractions:** Introduce Services, Actions, or Policies cleanly where business logic is shared or critical; do not over-engineer trivial CRUD.
- **Preserve existing business workflows:** Do not alter existing route contracts, order statuses, or booking semantics unless part of an approved task.
- **Keep changes minimal and focused:** Touch only files required for the approved scope.

---

## 4. Database Protection

- **Never reset the database** (`migrate:fresh`, `migrate:reset`, `db:wipe` on development/main databases are strictly forbidden unless explicitly authorized by the project owner for a test-only DB).
- **Never execute destructive migrations without approval.**
- **Never drop tables or columns without approval.**
- **Never modify existing migration files without explicit approval.** Always use new additive/altering migrations for approved schema changes.
- **Never delete production-like transaction records** (`pesanan`, `konfirmasi_pembayaran`, `keluhan`, or referenced `jadwal`).
- **Explain migration risks before proposing schema changes:** Include impact on foreign keys, indexes (e.g., InnoDB FK index dependencies), existing rows, nullability, and rollback behavior before writing or running migrations.

---

## 5. UI/UX Rules

- **Use one shared Design System** across Customer/Member, Operator Loket, and Admin once established and approved.
- **Maintain consistent typography, spacing, colors, and components** through centralized tokens and shared Blade components (avoid inline hex colors and duplicate status badge markup).
- **Adapt layouts to each role:** Share visual language while separating Public/Guest navigation from role-tailored authenticated shells and navigation.
- **Separate business logic from presentation:** Never hardcode mock arrays or query logic inside Blade views.
- **Avoid duplicated Blade markup:** Extract shared UI patterns and reusable form partials cleanly.
- **Maintain responsive behavior and accessibility:** Pair `<label for="...">` with matching `id`, provide accessible names (`aria-label`) on icon controls, and avoid blocking native `alert()`/`confirm()` dialogs in target components.
- **Pending Visual Specification:** Final color palettes, typography refinements, and visual references will be defined in a later approved Design System phase (`docs/13-design-system-preparation.md`).

---

## 6. Testing Rules

- **Test every changed feature** using automated tests targeting the isolated testing configuration (`gotix_testing` in `phpunit.xml` / `.env.testing`), never the main development database.
- **Preserve existing tests** (`tests/Feature/AuthTest.php`, `tests/Feature/ModelTest.php`) unless an intentional behavior change requires an approved test update.
- **Add regression tests for fixed bugs** (e.g., cancelled-seat re-booking, expired order enforcement, payment state transitions, complaint validation).
- **Report actual command outputs** verbatim for `php artisan test`, `vendor/bin/pint --test`, PHPStan/Larastan, and `npm run build`.
- **Never claim tests passed without successful execution.** If a tool fails or is blocked by the environment, report the exact failure/output and mark its verification status as `UNKNOWN`.

---

## 7. AI Communication & Execution Rules

Before every implementation task, follow this workflow:
1. **Understand the request** and verify relevant requirements.
2. **Inspect relevant files** (controllers, models, migrations, views, routes, tests).
3. **Identify dependencies** and side effects (database FKs, role permissions, CSS/Vite build).
4. **Explain the implementation plan** clearly before coding when changes span modules or affect database/workflows.
5. **Identify risks** (data loss, downtime, breaking changes, uncovered paths).
6. **Execute only the approved scope** — do not drift into unrequested refactors or UI redesigns.
7. **Report modified files and test results** honestly after completion, and do not commit or push to Git without explicit owner permission.

**When uncertain, ask the project owner instead of guessing or inventing requirements.**

