# Chhapa

Chhapa is a commercial printing catalogue. Guests browse published offerings by printer and product type. A printing business administrator signs in to configure products and variants, then chooses whether each offering is draft, published or archived.

## Local setup

Use PHP 8.2 or newer with PDO MySQL, PDO SQLite for tests, and the extensions required by Laravel 12 and PHPUnit 11. Install Composer 2 and have MySQL 8 available. For Apache, point the virtual host document root at the project's `public` directory and enable `mod_rewrite` and `AllowOverride FileInfo` for that directory. You can also use Laravel's local development server below.

1. Create a dedicated empty MySQL database, for example `chhapa_dev`, and give a local database account privileges on that database.
2. Run these commands from the repository root in PowerShell. If XAMPP PHP is installed outside PATH, add its PHP directory to this PowerShell session first.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

3. Set the local database credentials in `.env`. Set `CHHAPA_DEMO_PASSWORD` to a new local-only password between 12 and 72 bytes. Then run:

```powershell
php artisan migrate
php artisan db:seed
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). Sign in using the password you set.

| Demo role | Email | Catalogue |
| --- | --- | --- |
| Ink & Clay administrator | ink@example.test | Mugs and posters |
| Threadline administrator | thread@example.test | T-shirts |
| Customer | customer@example.test | Published offerings |

The demo seeder runs in the local environment and preserves existing records when rerun. Give it a dedicated development database.

## Testing

Run `composer test`. The automated suite uses in-memory SQLite and covers F01-T01 through F01-T12. Also migrate and exercise the seeded application against MySQL before collecting browser evidence. See [test results](docs/evidence/sprint-1/TEST_RESULTS.md) and the [screenshot manifest](docs/evidence/sprint-1/SCREENSHOT_MANIFEST.md).

## Sprint 1 implementation

- Eloquent models and migrations represent printers, users, products and variants. A nullable managed-business foreign key identifies a catalogue administrator's business.
- Session authentication handles sign-in. The product policy checks ownership, the admin controller saves through a business-scoped query, and public queries require published status.
- A Form Request validates product data and business-specific SKUs. The catalogue options model checks supported combinations. Product and variant changes are written in one transaction.
- Blade renders the workspace and catalogue. Vanilla JavaScript updates option choices and adds or removes variant inputs.

Initial options cover mugs, posters and T-shirts. Each business selects which valid combinations to offer. A single administrator account manages one business. Saving replaces the offering's variant rows. Preserve stable variant identities before future order records reference variants.

The team repository is [Alfa5nm/Chhapa](https://github.com/Alfa5nm/Chhapa).

## Verification and submission evidence

On 16 November 2026 (Asia/Dhaka), all 12 tests passed with 81 assertions on SQLite. The dedicated MariaDB database migrated and seeded successfully; real Chrome walkthroughs verified creation, editing, publication, draft privacy, validation and business isolation. Six genuine screenshots are under `docs/evidence/sprint-1/screenshots/` and inserted into `docs/Chhapa_CSE470_SRS_Sprint1_EVIDENCED.docx`. Original SRS preserved. See the evidence records and Sprint 1 report for limits and pending team/submission checks.

The project, tests, screenshots and SRS were subsequently committed and pushed to public `main` at the team's request. The initial implementation/evidence commit is [abf602a](https://github.com/Alfa5nm/Chhapa/commit/abf602a8289120d60ddf65560cb1e8ca731d5090). The SRS and capture records retain their original pre-publication state; see [publication record](docs/sprint-1/PUBLICATION.md) for the later GitHub update. Credentials, development databases, dependencies and runtime logs are excluded.

## AI assistance

ChatGPT Codex assisted with the Laravel starter, catalogue implementation, tests and documentation. Team implementation review, verification and individual contributions are to be recorded by the team.

## Existing Windows workspace

This workspace has a preconfigured, isolated XAMPP MariaDB 10.4 development instance. Local settings and generated credentials are in the ignored `.env`. Its data and server configuration are under the ignored `storage/catalogue-local-db` directory. The server is configured to listen only on 127.0.0.1:3307. If it is stopped, start it from the project root in a separate PowerShell window:

```powershell
& C:\xampp\mysql\bin\mysqld.exe "--defaults-file=$PWD\storage\catalogue-local-db\my.ini" --console
```

After dependency installation, use the existing .env and run php artisan migrate, php artisan db:seed, composer test, and php artisan serve.
