# Runbook

Catatan operasional & resep cepat untuk repo ini.

---

## Release flow: `dev` → `master` (squash merge)

Pola yang dipakai: kerjaan harian commit ke `dev`. Saat siap release, PR `dev → master` dengan **Squash and merge**. Setelah merge, **reset `dev`** agar sinkron ulang dengan `master` untuk cycle berikutnya.

### Langkah lengkap

1. **Push semua kerjaan ke `dev`**

   ```bash
   git switch dev
   git add -A
   git commit -m "feat: deskripsi singkat"
   git push origin dev
   ```

2. **Buat PR `dev → master` di GitHub**
   - Buka https://github.com/<owner>/<repo>/compare/master...dev
   - Klik **Create pull request**
   - Title: `Release vX.Y` (sesuai versi)

3. **Merge dengan Squash and merge**
   - Di halaman PR, klik dropdown tombol merge → pilih **Squash and merge**
   - Edit commit message kalau perlu (default = title PR)
   - Klik **Confirm squash and merge**

4. **Tunggu workflow Deploy selesai**
   - Workflow `Deploy` jalan otomatis saat push ke `master`
   - Cek tab **Actions** sampai status hijau

5. **Reset `dev` agar sinkron dengan `master`**

   ```bash
   git fetch origin
   git switch dev
   git reset --hard origin/master
   git push --force-with-lease origin dev
   ```

   Sekarang `dev = master`, siap untuk cycle berikutnya. PR berikutnya hanya akan menampilkan commit-commit baru.

### ⚠️ Sebelum reset, WAJIB cek:

```bash
git fetch origin

# Commit di dev yang BELUM di master (harusnya kosong setelah squash merge):
git log origin/master..origin/dev --oneline

# Diff isi file (harusnya minimal / hanya file kerja lokal yang untracked):
git diff origin/dev origin/master --stat
```

**Kalau output `git diff` menunjukkan file penting hilang di master** → artinya PR-mu belum termasuk kerjaan tersebut. **Jangan reset.** Buat PR baru dulu untuk membawa kerjaan itu ke master.

### Catatan keamanan

- `--force-with-lease` lebih aman dari `--force`: akan gagal kalau ada orang lain push ke `dev` setelah kamu fetch terakhir.
- File **untracked** di working tree tidak terpengaruh `git reset --hard` — aman.
- File **modified tapi belum di-commit** akan **hilang** kena `reset --hard`. Stash dulu kalau ada:
  ```bash
  git stash push -u -m "wip before reset"
  git reset --hard origin/master
  git push --force-with-lease origin dev
  git stash pop
  ```

---

## Deploy ke VPS

Otomatis via `.github/workflows/deploy.yml` saat push ke `master`. Bisa juga manual:

- Tab **Actions** → workflow **Deploy** → **Run workflow** → pilih branch `master`

### Secrets yang dibutuhkan

| Secret | Contoh | Catatan |
|---|---|---|
| `SSH_HOST` | `153.92.9.112` | IP/hostname VPS |
| `SSH_USER` | `u574639322` | User SSH |
| `SSH_PORT` | `65002` | Default 22 |
| `SSH_KEY` | (private key full) | Termasuk header `-----BEGIN...` |
| `DEPLOY_PATH` | `/home/u574639322/domains/jakpurwokerto.or.id/public_html` | Path absolut di server |
| `PHP_BIN` | `/opt/alt/php84/usr/bin/php` | Path PHP 8.4 CLI di server |

### Pre-deploy di VPS (sekali saja)

```bash
ssh user@host
cd $DEPLOY_PATH

# Bikin .env dengan APP_KEY, DB credentials, dll
nano .env

# Pastikan storage writeable (Laravel needs this)
mkdir -p storage/{app/public,framework/{cache/data,sessions,views},logs} bootstrap/cache
chmod -R u+rwX storage bootstrap/cache

# Symlink public storage
$PHP_BIN artisan storage:link
```

### Kalau deploy error "key not specified" atau "view path not found"

Config cache stale. Di server:

```bash
$PHP_BIN artisan config:clear
$PHP_BIN artisan view:clear
$PHP_BIN artisan cache:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan view:cache
```

---

## Hotfix di server (skip CI)

Kalau urgent edit di server tanpa lewat CI (mis. fix `.env`):

1. SSH ke server, edit langsung
2. **Jangan lupa** sync ke repo lokal supaya deploy berikutnya tidak overwrite:
   ```bash
   # di lokal:
   scp -P $SSH_PORT user@host:$DEPLOY_PATH/.htaccess ./
   git add .htaccess
   git commit -m "fix: sync .htaccess from server"
   ```

File yang **di-exclude rsync** (aman di-edit langsung di server, tidak akan ketimpa):
- `.env`
- `storage/app/*`, `storage/logs`, `storage/framework/*`
- `node_modules`

File yang **ikut rsync** (jangan edit langsung di server, akan ketimpa):
- Semua kode `app/`, `routes/`, `resources/`, `public/`
- `composer.json`, `package.json`
- `.htaccess` di root

---

## Troubleshooting cepat

| Gejala | Penyebab umum | Fix |
|---|---|---|
| 403 di homepage | `.htaccess` rewrite tidak jalan / hilang | Cek `.htaccess` di `$DEPLOY_PATH` ada & punya rewrite ke `public/` |
| 500 setelah deploy | Config/view cache punya path lama | `artisan config:clear && config:cache` |
| "No application encryption key" | `.env` belum punya `APP_KEY` saat `config:cache` jalan | Set `APP_KEY` di server, lalu clear+cache config |
| Upload bukti error 419 | CSRF token expired / cookie domain salah | Cek `SESSION_DOMAIN` di `.env`, bersihkan cache browser |
| Storage file 404 | Symlink `public/storage` belum dibuat | `$PHP_BIN artisan storage:link` |
| `composer install` gagal di CI | Lock file butuh PHP > runner version | Naikkan `php-version` di workflow YAML |
