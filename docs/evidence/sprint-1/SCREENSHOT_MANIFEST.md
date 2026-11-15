# Sprint 1 screenshot manifest

Publication update: these captures were subsequently published with source in commit `abf602a8289120d60ddf65560cb1e8ca731d5090` on public GitHub main. The uncommitted revision below describes capture time; see `docs/sprint-1/PUBLICATION.md`.

| SRS marker | Filename | Required evidence |
| --- | --- | --- |
| F01-01 | catalogue-dashboard.png | Owner workspace with draft and published products |
| F01-02 | catalogue-create-product.png | Configured product form and its variant |
| F01-03 | catalogue-product-details.png | Saved product with supported variants |
| F01-04 | catalogue-public.png | Published product and provider on the public site |
| F01-05 | catalogue-invalid-quantity.png | Server validation feedback for an invalid quantity |
| F01-06 | catalogue-tenant-check.png | Second business denied access or genuine authorization test output |

All six were captured on **16 November 2026, around 00:02–00:05 Asia/Dhaka**, from the actual Laravel application backed by the dedicated MariaDB 10.4.32 instance. Captures use installed Google Chrome through Playwright at 1440 × 1000; create and validation forms use full-page capture. Images are unedited original browser captures, visually inspected, with no credentials visible. Businesses/products are fictional.

| File | URL | Role / state |
| --- | --- | --- |
| catalogue-dashboard.png | http://127.0.0.1:8000/workspace | Ink & Clay admin; draft and published offerings |
| catalogue-create-product.png | http://127.0.0.1:8000/workspace/create | Ink & Clay admin; completed draft form before save |
| catalogue-product-details.png | http://127.0.0.1:8000/workspace/5 | Ink & Clay admin; saved draft and variant |
| catalogue-public.png | http://127.0.0.1:8000/catalogue/5 | Guest; same product after publication |
| catalogue-invalid-quantity.png | http://127.0.0.1:8000/workspace/5/edit | Ink & Clay admin; server rejects maximum 1 below minimum 12 |
| catalogue-tenant-check.png | http://127.0.0.1:8000/workspace/5/edit | Threadline admin; HTTP 403 denial |

All six markers are resolved in `docs/Chhapa_CSE470_SRS_Sprint1_EVIDENCED.docx`; original SRS preserved. Source revision: uncommitted local main, no SHA. `source-sha256.txt` records source hashes; `screenshot-sha256.txt` records image hashes and exact timestamps. The evidence product remains published with size 11 oz and range 12–500 after verifying and restoring an edit to 15 oz.
