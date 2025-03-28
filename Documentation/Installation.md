# ChatPion Installation and Setup

## System Requirements

Before installing ChatPion, ensure your server meets the following requirements:

- **PHP**: Version 7.4.0 or higher
- **MySQL**: Version 5.7 or higher
- **Web Server**: Apache with mod_rewrite enabled or Nginx
- **PHP Extensions**:
  - curl
  - json
  - mbstring
  - mysqli
  - xml
  - zip
  - gd
  - openssl
- **Other Requirements**:
  - Composer (for dependency management)
  - SSL Certificate (recommended for secure connections)

## Installation Steps

### 1. Server Setup

Ensure your web server is properly configured:

**For Apache**:
- Enable mod_rewrite
- Enable .htaccess files
- Set AllowOverride to All in your virtual host configuration

**For Nginx**:
- Configure URL rewriting according to CodeIgniter requirements

### 2. Database Setup

Create a new MySQL database for ChatPion:

```sql
CREATE DATABASE chatpion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'chatpion_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON chatpion.* TO 'chatpion_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Application Installation

#### Option 1: Manual Installation

1. Download the latest version of ChatPion
2. Extract the files to your web server directory
3. Navigate to the application directory
4. Install dependencies using Composer:

```bash
composer install
```

5. Set proper permissions:

```bash
chmod -R 755 .
chmod -R 777 application/cache
chmod -R 777 application/logs
chmod -R 777 upload
chmod -R 777 download
```

#### Option 2: Installation via Git

1. Clone the repository:

```bash
git clone https://github.com/yourusername/chatpion.git
```

2. Navigate to the application directory
3. Install dependencies using Composer:

```bash
composer install
```

4. Set proper permissions:

```bash
chmod -R 755 .
chmod -R 777 application/cache
chmod -R 777 application/logs
chmod -R 777 upload
chmod -R 777 download
```

### 4. Configuration

1. Rename `application/config/database.php.example` to `application/config/database.php`
2. Edit `application/config/database.php` and update the database connection settings:

```php
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'chatpion_user',
    'password' => 'your_password',
    'database' => 'chatpion',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

3. Rename `application/config/config.php.example` to `application/config/config.php`
4. Edit `application/config/config.php` and update the base URL:

```php
$config['base_url'] = 'https://your-domain.com/';
```

5. Set your encryption key:

```php
$config['encryption_key'] = 'your-random-encryption-key';
```

### 5. Database Initialization

Run the database migration script to create the necessary tables:

```bash
php index.php migrate
```

### 6. Create Admin User

Create an admin user through the command line:

```bash
php index.php create_admin --username=admin --password=your_password --email=admin@example.com
```

### 7. Final Steps

1. Clear the cache:

```bash
php index.php clear_cache
```

2. Set up cron jobs for scheduled tasks:

```
* * * * * php /path/to/your/application/index.php cron_job minutely > /dev/null 2>&1
0 * * * * php /path/to/your/application/index.php cron_job hourly > /dev/null 2>&1
0 0 * * * php /path/to/your/application/index.php cron_job daily > /dev/null 2>&1
0 0 * * 0 php /path/to/your/application/index.php cron_job weekly > /dev/null 2>&1
0 0 1 * * php /path/to/your/application/index.php cron_job monthly > /dev/null 2>&1
```

## Post-Installation Configuration

### Social Media Integration

To enable social media integration, you need to create applications on the respective platforms and configure them in ChatPion:

#### Facebook and Instagram

1. Create a Facebook Developer account
2. Create a new Facebook App
3. Configure the app settings
4. Add the required products (Facebook Login, Instagram Graph API, etc.)
5. Update the app credentials in ChatPion:
   - Navigate to Admin > Social Apps
   - Enter your App ID and App Secret
   - Configure the callback URL

#### Google

1. Create a Google Developer account
2. Create a new Google Project
3. Enable the required APIs
4. Create OAuth credentials
5. Update the credentials in ChatPion:
   - Navigate to Admin > Social Apps
   - Enter your Client ID and Client Secret
   - Configure the redirect URI

### Payment Gateway Integration

To enable payment processing, configure the payment gateway settings:

#### PayPal

1. Create a PayPal Developer account
2. Create a new PayPal App
3. Get your Client ID and Secret
4. Update the credentials in ChatPion:
   - Navigate to Admin > Payment Settings
   - Enter your PayPal Client ID and Secret
   - Configure the webhook URL

#### Stripe

1. Create a Stripe account
2. Get your API keys
3. Update the credentials in ChatPion:
   - Navigate to Admin > Payment Settings
   - Enter your Stripe Publishable Key and Secret Key
   - Configure the webhook URL

## Troubleshooting

### Common Issues

#### 1. 404 Not Found

- Ensure mod_rewrite is enabled
- Check .htaccess file permissions
- Verify base URL configuration

#### 2. Database Connection Error

- Check database credentials
- Ensure MySQL server is running
- Verify database user permissions

#### 3. File Upload Issues

- Check directory permissions
- Verify PHP file upload settings
- Check file size limits

#### 4. Cron Job Not Running

- Verify cron job syntax
- Check PHP CLI path
- Ensure proper permissions

### Getting Help

If you encounter issues not covered in this guide, you can:

1. Check the documentation
2. Search the knowledge base
3. Contact support
4. Visit the community forum