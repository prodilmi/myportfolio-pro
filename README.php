<?php
/**
 * README - Installation and Setup Instructions
 */
?>
# MyPortfolioPro - Smart Portfolio Management System

## Installation Guide

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache with mod_rewrite or Nginx)

### Installation Steps

1. **Download and Extract**
   - Extract all files to your web server directory
   - Ensure proper file permissions (755 for directories, 644 for files)

2. **Configure Database**
   - Edit `app/bootstrap.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_db_user');
     define('DB_PASS', 'your_db_password');
     define('DB_NAME', 'myportfolio_pro');
     ```
   - Change `BASE_URL` to match your installation path

3. **Create Database**
   - Create a new MySQL database:
     ```sql
     CREATE DATABASE myportfolio_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```

4. **Run Setup**
   - Visit `http://your-domain.com/myportfolio-pro/setup.php`
   - This will create all required tables

5. **Test Installation**
   - Visit `http://your-domain.com/myportfolio-pro/`
   - Create a new account
   - Start managing your portfolio

## Features

- ✓ User Authentication & Registration
- ✓ Multiple Portfolio Management
- ✓ Trade Tracking & History
- ✓ Broker Integration Support
- ✓ Real-time Notifications
- ✓ Performance Analytics
- ✓ Secure Data Storage (AES-256 Encryption)
- ✓ Responsive Design
- ✓ Mobile Compatible

## File Structure

```
myportfolio-pro/
├── app/
│   ├── bootstrap.php
│   ├── Core/
│   │   ├── Auth.php
│   │   ├── Database.php
│   │   ├── Encryption.php
│   │   ├── Request.php
│   │   └── Response.php
│   ├── Services/
│   │   ├── BrokerService.php
│   │   ├── NotificationService.php
│   │   └── TradeService.php
│   └── Database/
│       └── Migration.php
├── api/
│   ├── auth.php
│   ├── portfolios.php
│   ├── holdings.php
│   ├── trades.php
��   ├── brokers.php
│   ├── analytics.php
│   └── notifications.php
├── pages/
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── portfolios.php
│   ├── portfolio-detail.php
│   ├── trading-history.php
│   ├── broker-integration.php
│   ├── notifications.php
│   ├── settings.php
│   └── components/
│       ├── navbar.php
│       └── footer.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── dashboard.js
│       ├── trading-history.js
│       ├── broker-integration.js
│       └── notifications.js
├── database/
│   └── schema.php
├── index.php
├── setup.php
└── .htaccess
```

## Configuration

### Environment Variables
Edit `app/bootstrap.php` to set:
- Database connection details
- Application base URL
- Encryption key (important for security)

### Security
- Change the `ENCRYPTION_KEY` in `app/bootstrap.php` to a unique value
- Use strong database passwords
- Keep PHP and dependencies updated
- Enable HTTPS in production

## Support

For issues or questions, please contact support@myportfoliopro.com

## License

MIT License - See LICENSE file for details
