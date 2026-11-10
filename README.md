# GI&BA — Corporate Website

Corporate website for **GI&BA Promotion**, an Italian business specializing in personalized clothing, professional workwear, promotional products, and custom printing services.

Developed with Laravel, the website provides a modern, responsive platform for presenting the company's services, showcasing completed projects, and connecting with potential customers.

**Website:** https://giebapromotion.com/

## Overview

GI&BA Promotion, part of Shipping Dog SNC, is based in Cavalese, Trentino-Alto Adige, Italy.

The company offers personalized clothing and accessories, professional uniforms, and printing solutions for businesses, organizations, and individuals.

The website serves as the company's digital presence, allowing visitors to explore its services, browse a gallery of products and previous work, and request information or personalized quotations.

### Features

- **Responsive website:** Layout designed for desktop, tablet, and mobile devices.
- **Service presentation:** Dedicated sections highlighting the company's products, customization techniques, and professional services.
- **Product gallery:** Visual showcase of personalized clothing, accessories, workwear, and promotional materials.
- **Contact form:** Allows visitors to send inquiries and request information directly through the website.
- **Direct communication:** Integration with external communication channels, including WhatsApp and Facebook.
- **Business information:** Contact details and location information to help customers reach the company.

## Technology Stack

The application is built with the following technologies:

| Category                | Technology             |
| ----------------------- | ---------------------- |
| Backend                 | PHP, Laravel           |
| Frontend                | Blade, JavaScript, CSS |
| Asset bundling          | Vite                   |
| Package management      | Composer, pnpm         |
| Database                | MariaDB                |
| Web server              | FrankenPHP, Caddy      |
| Development environment | Docker, Docker Compose |

### Architecture

The project uses a containerized development environment to provide consistent application and database configurations.

The Docker Compose setup includes three primary services:

- **Application:** Laravel application served through FrankenPHP and Caddy.
- **Vite:** Frontend development server providing asset compilation and hot module replacement.
- **Database:** MariaDB instance with persistent storage.

## Getting Started

### Prerequisites

Ensure the following tools are installed:

- Git
- Docker
- Docker Compose

PHP, Composer, Node.js, and pnpm are managed within the application's containerized development environment.

### 1. Clone the Repository

```bash
git clone https://github.com/ismaelet/gieba.git
cd gieba
```

### 2. Configure Environment Variables

Configure the environment variables required by Docker Compose.

The application requires an encryption key and may require email credentials for its contact form.

For a local setup, create a `.env` file in the repository root and configure the required variables:

```dotenv
APP_KEY=
MAIL_USERNAME=
MAIL_PASSWORD=
```

Generate a Laravel application key after installing the dependencies, or reuse an existing valid development key.

Never commit production credentials or other sensitive environment values to version control.

### 3. Build and Start the Containers

Build the Docker images and start the services:

```bash
docker compose up -d --build
```

Check the running containers:

```bash
docker compose ps
```

### 4. Install Dependencies

Install the PHP dependencies:

```bash
docker compose exec app composer install
```

Install the frontend dependencies if they have not already been installed by the Vite service:

```bash
docker compose exec vite pnpm install --frozen-lockfile
```

### 5. Initialize Laravel

If a development application key has not been configured, generate one with Laravel:

```bash
docker compose exec app php artisan key:generate --show
```

Add the generated value to the `APP_KEY` variable used by Docker Compose, then recreate the application container to apply it.

Run the database migrations:

```bash
docker compose exec app php artisan migrate
```

Create the symbolic link for publicly accessible files:

```bash
docker compose exec app php artisan storage:link
```

Clear cached configuration:

```bash
docker compose exec app php artisan optimize:clear
```

### 6. Access the Application

Once the containers are running, the application is available at:

**http://localhost:8000**

The Vite development server is exposed on port `5173` and provides hot module replacement during frontend development.

## Development

### Docker Commands

Start the development environment:

```bash
docker compose up -d
```

Stop the containers:

```bash
docker compose down
```

View the application logs:

```bash
docker compose logs -f app
```

Restart the application service:

```bash
docker compose restart app
```

### Laravel Commands

Run Artisan commands inside the application container:

```bash
docker compose exec app php artisan <command>
```

For example, clear the application caches:

```bash
docker compose exec app php artisan optimize:clear
```

### Frontend Development

The dedicated Vite container handles frontend dependencies and development assets.

To inspect its logs:

```bash
docker compose logs -f vite
```

To build production-ready frontend assets:

```bash
docker compose exec vite pnpm run build
```

### Database

The local database runs in a dedicated MariaDB container.

Database connection settings are provided through the Docker Compose environment.

Database data is stored in a persistent Docker volume so it is preserved when containers are stopped or recreated.

## Project Structure

The repository follows a Laravel application structure, with the application code located inside the `app/` directory.

```text
gieba/
├── app/
│   ├── app/                 # Application logic
│   ├── bootstrap/           # Framework bootstrap
│   ├── config/              # Application configuration
│   ├── database/            # Migrations and seeders
│   ├── public/              # Public entry point and assets
│   ├── resources/           # Frontend resources and views
│   ├── routes/              # Application routes
│   ├── storage/             # Logs and generated files
│   ├── tests/               # Automated tests
│   ├── composer.json        # PHP dependencies
│   └── package.json         # Frontend dependencies
├── docker/                  # Container configuration
├── compose.yml              # Docker Compose services
└── README.md                # Project documentation
```

Some directories and configuration files may vary depending on the development branch.

## Deployment

The application can be deployed to a PHP-compatible hosting environment or a containerized infrastructure supporting Laravel.

Before deploying to production:

1. Configure production environment variables and database credentials.
2. Set `APP_ENV=production` and `APP_DEBUG=false`.
3. Install PHP dependencies with production optimizations.
4. Build the frontend assets using Vite.
5. Run the required database migrations.
6. Configure file storage and directory permissions.
7. Optimize Laravel's application caches.
8. Configure the web server to serve the Laravel `public/` directory over HTTPS.

Production secrets, database credentials, and application encryption keys must remain outside version control.

## Security

The repository is intended to contain application source code and configuration templates only.

Sensitive information such as production credentials, API keys, application secrets, and database backups must not be committed.

Environment-specific configuration should be managed through environment variables or secure deployment configuration.

## Project Information

**Client:** GI&BA Promotion — Shipping Dog SNC

**Location:** Cavalese, Trentino-Alto Adige, Italy

**Website:** https://giebapromotion.com/

**Development:** Ismaele Tuffanelli

## License

This project was developed for GI&BA Promotion.

The source code and associated assets are not automatically licensed for redistribution or commercial reuse. Any third-party dependencies remain subject to their respective licenses.
