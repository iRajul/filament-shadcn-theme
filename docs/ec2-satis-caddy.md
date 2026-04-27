# EC2 Satis + Caddy Deployment

This deployment serves the private Composer repository at `https://app.pullpkg.dev`.

Satis builds static Composer metadata and ZIP archives from the private GitHub repository. Caddy serves the generated files over HTTPS and protects every request with HTTP Basic Authentication.

## Architecture

```text
GitHub private repo
    -> composer/satis Docker image
    -> deploy/satis-caddy/public
    -> caddy:2-alpine
    -> https://app.pullpkg.dev
    -> Composer clients with username/password
```

## Prerequisites

- DNS `A` record for `app.pullpkg.dev` points to the EC2 public IP.
- EC2 security group allows inbound TCP `80` and `443`.
- Docker and Docker Compose plugin are installed on EC2.
- A GitHub fine-grained token can read `iRajul/filament-shadcn-theme`.

The GitHub token only needs repository contents read access.

## Server Setup

SSH into EC2, then install Docker if it is not already installed:

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl git
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo tee /etc/apt/keyrings/docker.asc >/dev/null
sudo chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list >/dev/null
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo usermod -aG docker "$USER"
```

Log out and SSH back in so the Docker group is applied.

## Deploy Files

Clone this package repo on the EC2 machine:

```bash
git clone https://github.com/iRajul/filament-shadcn-theme.git /opt/pullpkg
cd /opt/pullpkg/deploy/satis-caddy
```

Create Composer auth for Satis:

```bash
cp auth.json.example auth.json
nano auth.json
```

Use a GitHub token with read-only access to the private package repository:

```json
{
    "github-oauth": {
        "github.com": "github_pat_REPLACE_WITH_READ_ONLY_TOKEN"
    }
}
```

Create the Composer repository username/password:

```bash
cp users.caddy.example users.caddy
docker run --rm caddy:2-alpine caddy hash-password --plaintext 'change-this-password'
```

Put the generated hash into `users.caddy`:

```text
internal GENERATED_CADDY_HASH
```

Do not use the plaintext password in `users.caddy`.

## Build Satis

From `/opt/pullpkg/deploy/satis-caddy`:

```bash
docker compose run --rm satis
```

This writes `packages.json`, Composer metadata, and ZIP archives into `public/`.

## Start Caddy

```bash
docker compose up -d caddy
```

Caddy will request and renew the TLS certificate automatically for `app.pullpkg.dev`.

Check the service:

```bash
docker compose logs -f caddy
curl -I -u internal:change-this-password https://app.pullpkg.dev/packages.json
```

Expected result:

```text
HTTP/2 200
```

## Install From An Internal Laravel App

In the consuming Laravel app:

```bash
composer config repositories.pullpkg composer https://app.pullpkg.dev
composer config --global http-basic.app.pullpkg.dev internal change-this-password
composer require irajul/filament-shadcn-theme:^1.0 --prefer-dist
php artisan vendor:publish --tag=filament-shadcn-theme-config
```

## Rebuild After A New Release

After tagging and pushing a new package version:

```bash
cd /opt/pullpkg
git pull
cd deploy/satis-caddy
docker compose pull satis
docker compose run --rm satis
```

The Caddy container does not need a restart because it serves files from the mounted `public/` directory.

## Rotate Internal Composer Password

Generate a new password hash:

```bash
docker run --rm caddy:2-alpine caddy hash-password --plaintext 'new-password'
```

Update `users.caddy`, then reload Caddy:

```bash
docker compose exec caddy caddy reload --config /etc/caddy/Caddyfile
```

Update internal apps:

```bash
composer config --global http-basic.app.pullpkg.dev internal new-password
```
