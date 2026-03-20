# Laravel + Flask Project

This project runs **Laravel (PHP)** and **Flask (Python)** simultaneously in a **GitHub Codespace**.  
Follow the steps below to set up the environment and run both servers.

The demo video is available [here](https://binusianorg-my.sharepoint.com/personal/stefani_ganda_binus_ac_id/_layouts/15/guestaccess.aspx?share=IQBifYd5yhNlTLTBx8HhJQgNAf1gVYxoix7wpRa5HQt6EvI&e=wnsbNs).

---

## 1. Codespace Setup

When you open this repository in Codespaces, follow these steps:

### Update system packages

```bash
sudo apt update && sudo apt upgrade -y
````

## 2. Install PHP and Composer (for Laravel)

```bash
sudo apt install -y php php-cli php-mbstring php-xml php-bcmath php-curl unzip git curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installations
php -v
composer -V
```

## 3. Set up Laravel environment

Make sure the Laravel `.env` file exists:

```bash
cd /workspaces/laravel11-GooSe-2025

# Copy .env from example if missing
cp .env.example .env 2>/dev/null || echo ".env already exists"

# Generate application key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 4. Install Python 3 and Flask (for API)

```bash
# Install Python 3, pip, and venv
sudo apt install -y python3 python3-pip python3-venv

# Optional: make `python` point to `python3`
alias python=python3
```

Activate Python virtual environment and install Flask:

```bash
# Create venv if not already
python -m venv .venv

# Activate venv
source .venv/bin/activate

# Upgrade pip and install Flask
pip install --upgrade pip
pip install flask

# Verify Flask
flask --version
```

## 5. Running the servers

### Laravel (PHP) server

```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

### Flask (Python) API server

Insert the [AISAP model](https://drive.google.com/drive/folders/13xj4k_hw-c35O8OFoj1CwAynLYU-R-SX) into the folder.

Open a **new terminal** or **tab** in Codespaces:

```bash
# Activate venv
source .venv/bin/activate

# Run API
python api.py
```

Visit the API at the port specified in `api.py` (usually `http://127.0.0.1:5000`).

---

## Notes

* Laravel requires **PHP extensions**: `mbstring`, `xml`, `bcmath`, `curl`, `pdo_mysql`.
* Flask requires Python 3 and a virtual environment (`.venv`).
* If you restart your Codespace, you may need to **re-activate `.venv`** for Python and install missing PHP extensions.

## Optional: Make `python` permanent

Add this to your shell so you can run `python` instead of `python3`:

```bash
echo "alias python=python3" >> ~/.bashrc
source ~/.bashrc
```
