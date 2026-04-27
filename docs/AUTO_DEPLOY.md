# Auto Deploy (GitHub -> Server)

This project includes a workflow at `.github/workflows/deploy-web.yml`.
It deploys automatically when you push to `main` or `master`.

## 1) Add GitHub Repository Secrets

In GitHub: `Repo -> Settings -> Secrets and variables -> Actions -> New repository secret`

Create these secrets:

- `SERVER_HOST`: your server IP or domain
- `SERVER_PORT`: usually `22`
- `SERVER_USER`: SSH user on server
- `SERVER_SSH_KEY`: private SSH key (full key text)
- `SERVER_APP_PATH`: full path to app on server (example: `/var/www/towncore`)

## 2) Prepare Server One Time

Run these on your server once:

```bash
cd /var/www
git clone <YOUR_GITHUB_REPO_URL> towncore
cd towncore
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
```

Make sure these are installed on server:

- `git`
- `php`
- `composer`

## 3) Test Deployment

Push to `main` or `master` and open:

- `GitHub -> Actions -> Deploy Web App`

You should see the deploy job run automatically.

## Notes

- This workflow ignores `TowncoreMobile/**` changes.
- If your default branch is only `main`, you can remove `master` from the workflow.