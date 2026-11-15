# Sprint 1 test results

Publication update: the verified source and evidence were subsequently pushed to public GitHub main in commit `abf602a8289120d60ddf65560cb1e8ca731d5090`. The revision/private-visibility descriptions below refer to capture time; see `docs/sprint-1/PUBLICATION.md`.

## Current verification — 16 November 2026, Asia/Dhaka

All F01-T01–T12 passed again: **12 tests, 81 assertions**. Command: `C:\xampp\php\php.exe vendor\bin\phpunit --colors=never`. Raw output: [phpunit-output.txt](phpunit-output.txt). Environment: PHP 8.2.12, Laravel 12.72.0, PHPUnit 11.5.58, in-memory SQLite. Earlier blocked attempts below are historical and superseded by this run.

The dedicated MariaDB 10.4.32 instance was started on 127.0.0.1:3307 using its existing isolated configuration. `php artisan migrate --force`, `php artisan db:seed --force` and `php artisan migrate:status` succeeded through XAMPP PHP. No database reset was performed.

Real Chrome/Playwright checks against `http://127.0.0.1:8000` and MariaDB passed:

- Owner sign-in, workspace listing and complete offering creation (product 5, fictional SKU SPRINT1-EVIDENCE).
- Saved options, quantity, printable area and indicative price displayed correctly.
- Draft public detail returned HTTP 404; publication then exposed correct provider and options to a guest.
- Owner changed size to 15 oz; saved detail persisted it. Restored to 11 oz afterward.
- Maximum 1 below minimum 12 produced server validation; saved range remained 12–500.
- Threadline administrator received HTTP 403 at `/workspace/5/edit`.
- Provider/category filters showed Threadline's T-shirt and excluded Ink & Clay's mug.
- Guest detail at a 390 px viewport had no horizontal overflow (focused responsive check).

F01-A1–A6 passed using automated tests and these browser checks. All six real screenshots were captured. Browser checks used MariaDB; the full automated suite used SQLite. Apache, load/concurrency, comprehensive accessibility and Firefox/Edge were not tested.

Revision: uncommitted local main, no SHA or configured remote. `source-sha256.txt` fingerprints verified source. Connected GitHub metadata confirmed https://github.com/Alfa5nm/Chhapa exists and is private; no source was pushed. Faculty access, individual contributions and submission remain unconfirmed. Evidence collection occurred after the stated 15 November checkpoint.

## Earlier verification history

| Case | Scenario | Result |
| --- | --- | --- |
| F01-T01 | Owner creates a complete offering | Passed |
| F01-T02 | Variant edit persists | Passed |
| F01-T03 | Draft stays private | Passed |
| F01-T04 | Publish, unpublish and archive | Passed |
| F01-T05 | Zero and negative minimum are rejected | Passed |
| F01-T06 | Reversed quantity range is rejected | Passed |
| F01-T07 | Duplicate SKU within one company is rejected | Passed |
| F01-T08 | Another business cannot access or edit the offering | Passed |
| F01-T09 | Customer and guest cannot mutate admin catalogue | Passed |
| F01-T10 | Tampered variant and ownership values are rejected | Passed |
| F01-T11 | Public listing and detail have the correct provider | Passed |
| F01-T12 | Empty catalogue and malformed input are handled | Passed |

Run `composer test` and record the output, local environment, date, and Git SHA. Then rerun the browser walkthrough with the MySQL-backed demo and check F01-A1 through F01-A6. At this checkpoint, Composer and Laravel dependencies were unavailable in the execution environment, and the package registry was unreachable.

Observed local checks: PHP 8.2.12 syntax checks passed for 18 PHP entry points and source files. `node --check public/catalogue-form.js` passed. An isolated PHP check passed eight valid and invalid catalogue option combinations. `php artisan --version` stopped because `vendor/autoload.php` was absent. MySQL TCP access on localhost port 3306 returned Windows socket error 10013.

Second setup attempt: initialized a dedicated MariaDB 10.4.32 data directory, created the chhapa_dev schema and local-only database user, and generated the Laravel application key. MariaDB reported ready on 127.0.0.1:3307. PHP PDO from the execution environment was denied access to the socket. Packagist and the Composer download host still failed DNS resolution. Laravel integration checks remain pending.

Verified after Composer installed Laravel 12.72.0. The missing Laravel exception-handler registration in bootstrap/app.php was fixed. PHPUnit 11.5.58 passed all 12 feature tests and 81 assertions on PHP 8.2.12 with in-memory SQLite. The migration and demo seeder also completed against an isolated file-backed SQLite database. The browser timed out against the isolated server and MariaDB socket access from this session was denied. MariaDB verification and browser evidence are pending.
