# Intzi - Hyperlocal Multi-Service Platform

![Intzi Platform](https://img.shields.io/badge/version-1.0.0-blue) ![PHP](https://img.shields.io/badge/PHP-7.4+-purple) ![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange) ![License](https://img.shields.io/badge/license-MIT-green)

Intzi is a comprehensive hyperlocal service platform that connects users with local service providers for tailoring, beauty services, food/catering, and household help. The platform empowers financially dependent individuals by providing flexible income opportunities through dignified work.

## 🌟 Key Features

### For Customers
- 🔍 Browse and search local service providers
- 📅 Book services (instant or scheduled)
- 💳 Secure demo payment gateway
- ⭐ Rate and review service providers
- 📊 Track booking history
- 👤 Profile management

### For Service Providers
- 📝 Easy registration and approval process
- 📈 Professional dashboard
- 💼 Booking management (accept/reject requests)
- 💰 Earnings tracking and analytics
- ⭐ Review management
- 📱 Real-time booking notifications

### For Administrators
- 🛡️ Complete platform control
- ✅ Provider approval system
- 👥 User management
- 📊 Booking oversight
- 🏷️ Category management
- 📈 Revenue analytics

## 🛠 Technology Stack

**Frontend:**
- HTML5, CSS3, JavaScript
- Font Awesome 6.4.0
- Google Fonts (Poppins)
- Responsive Design

**Backend:**
- PHP 7.4+
- MySQL 8.0+
- Session-based Authentication
- Password Hashing (BCrypt)

**Server:**
- Apache (via XAMPP)
- phpMyAdmin for database management

## 📋 Prerequisites

Before installation, ensure you have:
- [XAMPP](https://www.apachefriends.org/) (v7.4 or higher)
- Web Browser (Chrome, Firefox, Edge recommended)
- Text Editor (VS Code, Sublime Text, etc.)

## 🚀 Installation Guide

### Step 1: Setup XAMPP

1. Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Extract Project Files

1. Extract the project ZIP file
2. Copy the `intzi_db` folder to:
   - **Windows:** `C:\xampp\htdocs\`
   - **Mac:** `/Applications/XAMPP/htdocs/`
   - **Linux:** `/opt/lampp/htdocs/`

### Step 3: Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **"New"** to create a database
3. Database name: `intzi_db`
4. Click **"Import"** tab
5. Choose file: `intzi_database.sql`
6. Click **"Go"** to import

**Alternative:** Run this SQL in phpMyAdmin:
CREATE DATABASE intzi_db;

Then import the `intzi_database.sql` file.

### Step 4: Configuration

Open `config.php` and verify settings:

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intzi_db');
define('BASE_URL', 'http://localhost/intzi_db/');


### Step 5: Access the Application

Open your browser and navigate to:
- **Customer Site:** `http://localhost/intzi_db/`
- **Provider Portal:** `http://localhost/intzi_db/provider-login.php`
- **Admin Panel:** `http://localhost/intzi_db/admin-login.php`

## 📁 Project Structure

intzi_db/
├── config.php # Database & app configuration
├── payment-config.php # Demo payment gateway config
├── intzi_database.sql # Database structure & sample data
│
├── Customer Portal (9 files)
│ ├── index.php # Homepage
│ ├── services.php # Service provider listing
│ ├── service-details.php # Provider profile page
│ ├── login.php # Customer login
│ ├── register.php # Customer registration
│ ├── booking.php # Booking form
│ ├── my-bookings.php # Booking history
│ ├── profile.php # User profile
│ └── logout.php # Logout handler
│
├── Payment System (5 files)
│ ├── payment.php # Payment gateway page
│ ├── process-payment.php # Payment processing
│ ├── payment-success.php # Success page
│ ├── payment-failed.php # Failed page
│ └── write-review.php # Review submission
│
├── Provider Portal (8 files)
│ ├── provider-register.php # Provider registration
│ ├── provider-login.php # Provider login
│ ├── provider-dashboard.php # Dashboard overview
│ ├── provider-bookings.php # Booking management
│ ├── provider-profile.php # Profile editing
│ ├── provider-earnings.php # Earnings analytics
│ ├── provider-reviews.php # Review management
│ ├── provider-logout.php # Logout handler
│ └── update-booking-status.php # Booking status handler
│
├── Admin Panel (7 files)
│ ├── admin-login.php # Admin login
│ ├── admin-dashboard.php # Admin overview
│ ├── admin-providers.php # Provider management
│ ├── admin-users.php # User management
│ ├── admin-bookings.php # Booking oversight
│ ├── admin-categories.php # Category management
│ ├── admin-update-provider.php # Provider status handler
│ └── admin-logout.php # Logout handler
│
└── images/
└── providers/ # Provider profile images
└── default-provider.jpg


**Total Files:** 34

## 🗄 Database Schema

### Core Tables

**users** - Customer accounts
- user_id, full_name, email, password, phone, address, city, created_at

**service_providers** - Service provider accounts
- provider_id, provider_name, email, password, phone, category_id, bio, experience_years, hourly_rate, rating, total_bookings, availability_status, account_status, skills, address, city, profile_image

**service_categories** - Service categories
- category_id, category_name, description, category_icon

**bookings** - Service bookings
- booking_id, user_id, provider_id, category_id, booking_type, booking_date, booking_time, duration_hours, total_amount, payment_status, payment_method, payment_date, booking_status, address, special_requirements

**reviews** - Customer reviews
- review_id, booking_id, user_id, provider_id, rating, review_text, created_at

**admins** - Administrator accounts
- admin_id, username, email, password, full_name, role, created_at

## 👤 Default User Accounts

### Admin Account
- **URL:** `http://localhost/intzi_db/admin-login.php`
- **Username:** `admin`
- **Password:** `admin123`

### Test Provider Account
Create via: `http://localhost/intzi_db/provider-register.php`
(Requires admin approval)

### Test Customer Account
Create via: `http://localhost/intzi_db/register.php`

## 🔄 System Workflows

### Customer Booking Flow
1. Customer browses services → Selects provider
2. Fills booking form (date, time, duration, address)
3. Proceeds to payment gateway
4. Completes demo payment (95% success rate)
5. Booking confirmed → Provider notified
6. After service completion → Write review

### Provider Management Flow
1. Provider registers → Account status: `pending`
2. Admin reviews application in admin panel
3. Admin approves/rejects provider
4. If approved → Provider can login and access dashboard
5. Provider receives booking requests
6. Provider accepts/rejects bookings
7. After service → Mark as completed
8. View earnings and reviews

### Admin Approval Flow
1. Admin logs into admin panel
2. Views pending provider registrations
3. Reviews provider details (experience, skills, rates)
4. Approves or suspends provider account
5. Manages all bookings, users, and categories
6. Monitors platform revenue

## 💳 Demo Payment Gateway

The platform includes a **demo payment gateway** for testing purposes:

### Features
- Realistic payment interface
- Multiple payment methods (Card, UPI, Net Banking)
- 95% success rate simulation
- Transaction ID generation
- Payment status tracking

### Test Card Details
Card Number: 4111 1111 1111 1111
CVV: Any 3 digits (e.g., 123)
Expiry: Any future date (e.g., 12/28)
Name: Any name


### Test UPI
UPI ID: success@intzi


**Note:** This is a demo gateway. No real money is processed.

## ⚙️ Configuration Options

### Base URL
Update in `config.php` if folder name is different:
define('BASE_URL', 'http://localhost/your_folder_name/');


### Payment Settings
Edit `payment-config.php`:
define('PAYMENT_SERVICE_FEE', 50); // Platform fee in INR


### Email Settings
For production, configure email notifications in `config.php`.

## 🐛 Troubleshooting

### Issue: "Cannot connect to database"
**Solution:**
1. Verify MySQL is running in XAMPP
2. Check credentials in `config.php`
3. Ensure database `intzi_db` exists

### Issue: "Column 'password' doesn't exist"
**Solution:**
ALTER TABLE service_providers
ADD COLUMN email VARCHAR(100) UNIQUE AFTER provider_name,
ADD COLUMN password VARCHAR(255) AFTER email;


### Issue: "Call to undefined function"
**Solution:**
Ensure you're using the latest `config.php` with all helper functions.

### Issue: Admin login fails
**Solution:**
Run `create-admin.php` to recreate admin account (provided in installation guide).

### Issue: Styles not loading
**Solution:**
1. Clear browser cache (Ctrl+F5)
2. Check BASE_URL in config.php
3. Verify file paths are correct

## 🔐 Security Notes

**For Development:**
- ✅ Password hashing with BCrypt
- ✅ SQL injection prevention with mysqli_real_escape_string
- ✅ Session-based authentication
- ✅ Input sanitization

**For Production:**
- Use prepared statements instead of sanitize()
- Enable HTTPS
- Set strong passwords
- Configure proper file permissions
- Add CSRF protection
- Implement rate limiting
- Use environment variables for sensitive data

## 🚀 Future Enhancements

Potential features to add:
- Real payment gateway integration (Razorpay/Stripe)
- Email/SMS notifications
- Real-time chat between customer and provider
- Mobile app (React Native/Flutter)
- Advanced search filters
- Provider availability calendar
- Multi-language support
- Geolocation-based provider search
- Provider verification badges
- Subscription plans for premium providers
- Referral system
- Coupon/discount codes
- Provider portfolios (photo galleries)
- Video call consultations
- Automated booking reminders

## 📱 Browser Compatibility

Tested and working on:
- ✅ Google Chrome 90+
- ✅ Mozilla Firefox 88+
- ✅ Microsoft Edge 90+
- ✅ Safari 14+
- ✅ Opera 76+

## 📄 License

This project is open-source and available under the MIT License.

## 👨‍💻 Developer

**Project:** Intzi - Hyperlocal Service Platform  
**Version:** 1.0.0  
**Developed:** November 2025  
**Tech Stack:** PHP, MySQL, HTML5, CSS3, JavaScript  

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review database structure in phpMyAdmin
3. Verify all files are properly uploaded
4. Check XAMPP error logs

## 📞 Contact

For additional help or custom development:
- Create test accounts and explore all features
- Document any bugs with screenshots
- Suggest improvements and new features

## 🎉 Acknowledgments

- Font Awesome for icons
- Google Fonts for Poppins typeface
- XAMPP for local development environment
- Community for inspiration and support

---

### Quick Start Commands

Start XAMPP services
sudo /opt/lampp/lampp start # Linux

or use XAMPP Control Panel on Windows/Mac
Access Application
Customer: http://localhost/intzi_db/
Provider: http://localhost/intzi_db/provider-login.php
Admin: http://localhost/intzi_db/admin-login.php
Stop XAMPP
sudo /opt/lampp/lampp stop # Linux


---

**Built with ❤️ for connecting communities with local service providers**

**Status:** ✅ Production Ready for Demo/Presentation
