# Adolices - School Management System

## About the Project
Adolices is a Laravel-based school management system that integrates with Rocket.Chat and HelloAsso for communication and payment processing. The system is built with Laravel 12 and uses modern web technologies including Tailwind CSS for styling.

## System Requirements
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (default) or MySQL/PostgreSQL
- Redis (optional, for caching)
- Git

## Project Dependencies

### Core Dependencies
- **Laravel Framework 12**: Core PHP framework
- **League HTML to Markdown**: Converts HTML content to Markdown format
- **Smalot PDF Parser**: For PDF file processing and parsing

### Frontend Dependencies
- **Vite**: Build tool and development server
- **Tailwind CSS**: Utility-first CSS framework
- **Axios**: HTTP client for API requests
- **PostCSS**: CSS processing tool
- **Autoprefixer**: PostCSS plugin for vendor prefixes

### Development Dependencies
- **Laravel Debugbar**: Debug toolbar for development
- **Laravel Pint**: Code style fixer
- **Laravel Sail**: Docker development environment
- **Laravel Pail**: Real-time log viewer
- **PHPUnit**: Testing framework
- **Faker**: Data generation for testing

### External Services
- **Rocket.Chat**: For communication features
- **HelloAsso**: For payment processing and membership management

## Local Development Setup

1. **Clone the Repository**
```bash
git clone [repository-url]
cd adolices
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Environment Variables**
Edit the `.env` file with your local settings. Key configurations include:
- Database settings
- Rocket.Chat integration
- HelloAsso API credentials
- Mail settings
- Application URL
- `APP_DEV`: Set to `false` in production to prevent test data from being seeded into the database

5. **Database Setup**
```bash
php artisan migrate
php artisan db:seed
```

6. **Start Development Servers**
```bash
# Using the development script (recommended)
composer dev

# Or manually:
php artisan serve
npm run dev
```

## Development Guidelines

### Available Development Tools
- **Laravel Debugbar**: Available in development for debugging
- **Laravel Pint**: Code style fixer (run with `./vendor/bin/pint`)
- **Laravel Sail**: Docker development environment (run with `./vendor/bin/sail up`)
- **Laravel Pail**: Real-time log viewer (included in `composer dev`)

### Development Workflow
1. **Code Style**
   - Follow PSR-12 standards
   - Use Laravel Pint for code formatting
   - Run `./vendor/bin/pint` before committing

2. **Testing**
   - Write tests for new features
   - Run tests with `php artisan test`
   - Maintain test coverage for critical features

3. **Database Changes**
   - Create migrations for all database changes
   - Include rollback methods in migrations
   - Test migrations with `php artisan migrate:fresh`

4. **Git Workflow**
   - Create feature branches from `main`
   - Use meaningful commit messages
   - Submit pull requests for review

### Common Development Commands
```bash
# Start development environment
composer dev

# Run tests
php artisan test

# Format code
./vendor/bin/pint

# Clear caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:meta
```

## Production Deployment

### Server Requirements
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Web server (Apache/Nginx)
- SSL certificate
- Database server (MySQL/PostgreSQL recommended for production)
- Redis (recommended for production)

### Deployment Steps

1. **Server Setup**
   - Install required software packages
   - Configure web server (Apache/Nginx)
   - Set up SSL certificate
   - Configure PHP-FPM
   - Set up database server

2. **Application Deployment**
```bash
# Clone repository
git clone [repository-url]
cd adolices

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure production environment
# Edit .env file with production settings
```

3. **Database Setup**
```bash
php artisan migrate --force
php artisan db:seed --force
```

4. **Optimize Application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

5. **Configure Queue Worker**
```bash
# Set up supervisor for queue worker
php artisan queue:work --queue=high,default,low --tries=3
```

6. **Configure Cron Jobs**
```bash
# Add to crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Production Environment Variables
Key production environment variables to configure:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- Database credentials
- Mail settings
- Rocket.Chat credentials
- HelloAsso API credentials
- Queue and cache settings

### Detailed Environment Variables Guide
Here's a detailed explanation of important environment variables:

#### Application Settings
- `APP_DEBUG`: Set to false when deploying
- `APP_LOCALE`: Default application language (default: en)
- `APP_FALLBACK_LOCALE`: Fallback language if translation is missing
- `APP_FAKER_LOCALE`: Locale for generating fake data

#### School Settings
- `FIRST_SCHOOL_YEAR`: The first year adhérents records (default: 2022)
- `ADHESION_MONTH_DAY`: Date format for membership renewal (default: 07-31)

#### External Services
- `ROCKET_CHAT_URL`: Your Rocket.Chat server URL
- `ROCKET_CHAT_USER_ID`: Bot user ID for Rocket.Chat integration
- `ROCKET_CHAT_AUTH_TOKEN`: Authentication token for Rocket.Chat
- `HELLOASSO_API_KEY`: API key for HelloAsso integration
- `HELLOASSO_API_SECRET`: API secret for HelloAsso integration
- `HELLOASSO_ORGANIZATION_SLUG`: Your HelloAsso organization identifier

#### Database and Cache
- `DB_CONNECTION`: Database type (sqlite, mysql, pgsql)
- `QUEUE_CONNECTION`: Queue driver (database recommended for production)
- `CACHE_STORE`: Cache driver (database recommended for production)

## Maintenance

### Regular Tasks
1. **Database Backups**
   - Set up automated database backups
   - Regular backup verification

2. **Log Management**
   - Configure log rotation
   - Monitor error logs

3. **Updates**
   - Regular composer updates
   - Regular npm updates
   - Security patches

### Monitoring
- Set up server monitoring
- Configure error reporting
- Monitor queue workers
- Set up uptime monitoring

## Security Considerations

1. **File Permissions**
   - Set proper permissions for storage and bootstrap/cache directories
   - Secure .env file
   - Configure proper web server permissions

2. **SSL/TLS**
   - Ensure SSL certificate is valid
   - Configure secure headers
   - Enable HTTPS-only cookies

3. **API Security**
   - Secure API endpoints
   - Implement rate limiting
   - Use secure tokens for external services

## Troubleshooting

### Common Issues
1. **Queue Worker Issues**
   - Check supervisor configuration
   - Verify queue connection settings
   - Monitor failed jobs

2. **Cache Issues**
   - Clear application cache
   - Verify Redis configuration
   - Check file permissions

3. **Database Issues**
   - Verify database connection
   - Check migration status
   - Monitor database performance

## Support
For support and issues, please contact the development team or create an issue in the repository.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
