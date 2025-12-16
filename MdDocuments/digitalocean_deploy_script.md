# Deploy to DigitalOcean (Placeholder Script)

This script provides a basic outline for deploying a Laravel/Vue application to a DigitalOcean server. **This is a placeholder and requires customization** with your specific server details, SSH keys, and project paths.

```bash
#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Configuration variables
# Replace with your actual DigitalOcean server IP/hostname
SSH_HOST="your_digitalocean_server_ip_or_hostname"
# Replace with your SSH user (e.g., 'root' or a deploy user)
SSH_USER="your_ssh_user"
# Replace with the absolute path to your application on the server
DEPLOY_PATH="/var/www/your_app_name"
# Replace with your application's branch to deploy
GIT_BRANCH="main"

echo "Starting deployment to ${SSH_USER}@${SSH_HOST}..."

# SSH into the server and execute commands
ssh -o StrictHostKeyChecking=no ${SSH_USER}@${SSH_HOST} << 'EOF'
    echo "Navigating to deployment path: ${DEPLOY_PATH}"
    cd "${DEPLOY_PATH}"

    echo "Pulling latest changes from Git branch: ${GIT_BRANCH}"
    git pull origin "${GIT_BRANCH}"

    echo "Installing Composer dependencies..."
    composer install --no-dev --prefer-dist --optimize-autoloader

    echo "Installing Node.js dependencies and building assets..."
    npm install --production # or yarn install --production
    npm run build # or yarn build

    echo "Running database migrations..."
    php artisan migrate --force

    echo "Clearing and caching configuration..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan config:cache

    echo "Optimizing application..."
    php artisan optimize

    echo "Reloading FPM (if applicable, adjust for your web server setup, e.g., Nginx with PHP-FPM)"
    # sudo systemctl reload php8.x-fpm # Adjust PHP version as needed
    # sudo systemctl restart nginx # Or apache2

    echo "Deployment completed successfully!"
EOF

echo "Deployment process finished."
```

**Instructions for integration with GitHub Actions (`.github/workflows/ci.yml`):**

1.  **Save this script** to your project, for example, `deploy.sh` in the root directory or a `scripts/` folder.
2.  **Make the script executable**: `chmod +x deploy.sh`
3.  **Update `ci.yml`**: Modify the `deploy` job in `ci.yml` to call this script.
4.  **Add SSH Keys to GitHub Secrets**: For the `ssh` command to work in GitHub Actions, you'll need to store your server's SSH private key as a GitHub Secret (e.g., `SSH_PRIVATE_KEY`). The `ssh` command would then need to be adapted to use this key (e.g., using `webfactory/ssh-agent` action).

**Example `ci.yml` snippet for deploy job:**

```yaml
  deploy:
    runs-on: ubuntu-latest
    needs: build-and-test
    if: github.ref == 'refs/heads/main'

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup SSH Agent
        uses: webfactory/ssh-agent@v0.7.0
        with:
          ssh-private-key: ${{ secrets.SSH_PRIVATE_KEY }}

      - name: Make deploy script executable
        run: chmod +x ./deploy.sh # Adjust path if needed

      - name: Run deployment script
        run: ./deploy.sh
        env:
          SSH_HOST: ${{ secrets.DIGITALOCEAN_SSH_HOST }} # Store your host in GitHub Secrets
          SSH_USER: ${{ secrets.DIGITALOCEAN_SSH_USER }} # Store your user in GitHub Secrets
          DEPLOY_PATH: /var/www/your_app_name # Replace with actual path
          GIT_BRANCH: main # Or your deployment branch
```
