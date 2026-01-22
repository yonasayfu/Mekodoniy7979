# Mekodonia Home Connect

Mekodonia Home Connect is a web application designed to facilitate connection and support between donors and elders in need. This platform enables donors to make one-time or recurring pledges, schedule visits, and track the impact of their contributions through an intuitive dashboard and timeline.

## Features

### Guest Donation (Phase 1)
- **One-Time Donation Form**: Allows guests to make donations quickly and easily.
- **Telebirr Integration (Mock)**: Simulates payment processing for one-time donations.

### Core Platform (Phase 2)
- **User Authentication & Authorization**: Secure login and registration with role-based access control.
- **Multi-tenancy**: Branch-level data isolation ensures Branch Admins only access data relevant to their assigned branch.
- **Branch Management**: CRUD interface for Super Admins to manage organizational branches.
- **Elder Management**: CRUD interface for Branch Admins to manage elder profiles, including personal details, priority levels, health status, special needs, monthly expenses, and profile media (images/videos).

### Donor Experience (Phase 3)
- **Donor Registration**: Enhanced registration form to capture donor-specific information (address, phone, date of birth, gender).
- **Donor Dashboard**: Personalized dashboard for registered donors to view their contributions, supported elders, and quick actions.
- **Pledging (Adopting an Elder)**: Functionality for donors to pledge support to specific elders, with options for amount and frequency.
- **Auto-Payment (Mock)**: Simulated recurring payment setup for donors.

### Advanced Features (Phase 4)
- **Visit Scheduler**: Allows users to schedule visits to elders, with status tracking (pending, approved, rejected, completed).
- **Impact Timeline**: Provides a chronological feed of events related to a donor's contributions and their impact.
- **Offline Support (PWA)**: Configured as a Progressive Web App for improved performance and offline accessibility.
- **Reporting**: Global reports for Super Admins to gain insights into donations, elders, and overall operations.
- **Automation**: Cron jobs for sending reminders and future automated tasks.

## Getting Started

Follow these instructions to set up and run the project locally.

### PostgreSQL setup

The application expects a PostgreSQL server running at `127.0.0.1:5432` (see the `DB_HOST`/`DB_PORT` values in `.env`). If you run into `SQLSTATE[08006] connection to server ... failed: Connection refused`, the database service is either not running or listening on a different port.

- On macOS, install PostgreSQL via Homebrew and start it with `brew services start postgresql`.
- On Linux, use `sudo systemctl start postgresql` (or whatever init system you rely on).
- The provided `docker-compose.example.yml` spins up Postgres as `db`; run `docker compose -f docker-compose.example.yml up -d db` and either change the port mapping to `5432:5432` or update `DB_PORT`/`DB_HOST` in `.env` to match the exposed port (`54320` by default).

Verify connectivity with `pg_isready`, `psql -h 127.0.0.1 -U postgres -p 5432`, or `php artisan migrate` once the service is available.

If you'd rather work with SQLite during development, switch `DB_CONNECTION=sqlite` in `.env` and run `touch database/database.sqlite` before running the start-up commands.

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 20 or higher
- npm or Yarn
- PostgreSQL database

### Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/yonasayfu/BasicBoilerPlateForLaravel.git .
    ```
    (Note: The actual repository name should be replaced if it's different)

2.  **Install PHP Dependencies**:
    ```bash
    composer install
    ```

3.  **Install Node.js Dependencies**:
    ```bash
    npm install
    # or
    yarn install
    ```

4.  **Environment Setup**:
    Copy the example environment file and generate an application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Edit the `.env` file to configure your database connection (ensure `DB_CONNECTION=pgsql` and provide your PostgreSQL credentials).

5.  **Database Migration and Seeding**:
    Run the database migrations and seed the database with initial data:
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Build Frontend Assets**:
    ```bash
    npm run build
    # or
    yarn build
    ```

7.  **Serve the Application**:
    ```bash
    php artisan serve
    ```
    The application will be accessible at `http://127.0.0.1:8000`.

## Running Tests
```bash
./vendor/bin/pest
```

## Code Style & Linting
```bash
vendor/bin/pint
npm run format
npm run lint
```

## Deployment
A basic GitHub Actions workflow `ci.yml` is set up for Continuous Integration, including linting, testing, and a placeholder for deployment to DigitalOcean on pushes to the `main` branch.

## PWA Setup
The application is configured as a Progressive Web App (PWA) using `VitePWA`. This includes a manifest and placeholder icons for offline capabilities and improved user experience.

## Contributing
(Instructions for contributing to the project, if applicable)

## License
(Project license information)

---
*Generated by Gemini CLI*
