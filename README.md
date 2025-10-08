# B‑Smart Services — Static Site (GitHub Pages)

Single‑file site + extras (404 page, custom domain template).

## Deploy on GitHub Pages
1. Create a **public** repo and upload everything in this zip.
2. In GitHub: **Settings → Pages** → Source: *Deploy from a branch* → Branch: `main` → `/ (root)` → **Save**.
3. Your site will publish at `https://<username>.github.io/<repo>/`.

## Custom domain (CNAME)
- Edit `CNAME.sample` and put **your real domain** (e.g. `www.yourdomain.com`).
- Rename the file to exactly **`CNAME`** (no extension) and commit.
- In your DNS, add a CNAME record: `www` → `<username>.github.io.`
- Back in GitHub: **Settings → Pages → Custom domain** → enter your domain and enable **Enforce HTTPS**.

> If you want the naked domain (`yourdomain.com`) to work, add an ALIAS/ANAME or A‑records as per GitHub Pages docs.

## 404 page
- `404.html` is included. GitHub Pages will automatically show it for missing routes.
- The page includes buttons to go **Home** and to **Contact**.

## Replace these placeholders
- `images/logo-bsmart.png` — your logo file.
- `images/favicon.ico`, `images/apple-touch-icon.png`, `images/og-bsmart.jpg` — favicons & social preview.
- In `index.html`: replace `YOUR_ID` in the Formspree action URL.
- Update the email/phone and LinkedIn URL (search for `data-linkedin`).

## Notes
- All asset paths are **relative** (`images/...`) for subpath deployments.
- Testimonials are **pending** by default; open `index.html#moderate` to approve them.

