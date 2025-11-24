# Intzi - Hyperlocal Service Platform

Intzi is a hyperlocal, multi-service platform that connects users with local service providers for immediate and scheduled bookings across tailoring, beauty services, food/catering, and household help. The platform empowers financially dependent individuals by providing flexible income opportunities through dignified work.

## 📋 Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [File Structure](#file-structure)
- [Usage](#usage)
- [User Features](#user-features)
- [Screenshots](#screenshots)
- [Troubleshooting](#troubleshooting)
- [Future Enhancements](#future-enhancements)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### For Users
- **Browse Services**: Explore local service providers across multiple categories
- **Advanced Filtering**: Sort by rating, price, bookings, and category
- **Provider Profiles**: View detailed provider information, skills, reviews, and ratings
- **Instant & Scheduled Booking**: Book services immediately or schedule for later
- **Booking Management**: Track all bookings with status updates
- **Secure Authentication**: User registration and login system
- **Profile Management**: Update personal information and preferences
- **Review System**: Rate and review service providers after service completion

### For Service Providers
- **Professional Profiles**: Showcase skills, experience, and portfolio
- **Rating & Reviews**: Build reputation through customer feedback
- **Availability Management**: Set availability status
- **Booking Notifications**: Receive booking requests from customers

## 🛠 Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL (via phpMyAdmin)
- **Server**: XAMPP (Apache + MySQL)
- **Styling**: Custom CSS with Poppins Font (Google Fonts)
- **Icons**: Font Awesome 6.4.0
- **Design**: Responsive Blue-themed UI

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- [XAMPP](https://www.apachefriends.org/download.html) (v7.4 or higher)
  - Apache Server
  - MySQL Database
  - PHP 7.4+
- Web Browser (Chrome, Firefox, Edge, or Safari)
- Text Editor (VS Code, Sublime Text, or any IDE)

## 🚀 Installation

### Step 1: Download/Clone Project

1. Download or clone this repository
2. Extract the project folder if downloaded as ZIP

### Step 2: Place in XAMPP Directory

1. Copy the entire `intzi_db` folder
2. Paste it into your XAMPP `htdocs` directory
   - **Windows**: `C:\xampp\htdocs\`
   - **Mac**: `/Applications/XAMPP/htdocs/`
   - **Linux**: `/opt/lampp/htdocs/`

### Step 3: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** server
3. Start **MySQL** database
4. Verify both show "Running" status

## 🗄 Database Setup

### Method 1: Using phpMyAdmin

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** in the left sidebar
3. Create a database named: `intzi_db`
4. Click on the `intzi_db` database
5. Go to the **"Import"** tab
6. Click **"Choose File"** and select `intzi_database.sql`
7. Click **"Go"** to import the database
8. Verify all tables are created successfully:
   - users
   - service_categories
   - service_providers
   - bookings
   - reviews

### Method 2: Using MySQL Command Line
mysql -u root -p
CREATE DATABASE intzi_db;
USE intzi_db;
SOURCE /path/to/intzi_database.sql;


## ⚙ Configuration

### Database Configuration

Open `config.php` and verify/update the database credentials:

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your MySQL username
define('DB_PASS', ''); // Your MySQL password (empty by default)
define('DB_NAME', 'intzi_db'); // Database name


### Base URL Configuration

Update the base URL in `config.php` if your folder name is different:
define('BASE_URL', 'http://localhost/intzi_db/');


Replace `intzi_db` with your actual folder name.

## 📁 File Structure

intzi_db/
├── config.php # Database configuration
├── index.php # Homepage
├── services.php # Service providers listing
├── service-details.php # Provider profile page
├── login.php # User login
├── register.php # User registration
├── booking.php # Booking form
├── my-bookings.php # User bookings dashboard
├── profile.php # User profile management
├── logout.php # Logout handler
├── intzi_database.sql # Database structure & sample data
├── README.md # This file
└── images/
└── providers/ # Provider profile images
└── default-provider.jpg


## 🎯 Usage

### Accessing the Application

1. Open your web browser
2. Navigate to: `http://localhost/intzi_db/`
3. You should see the Intzi homepage

### Default Test Credentials

The database comes with sample data. You can create a new account or use these test credentials:

**Test User Account** (You'll need to register first)
- Email: `test@example.com`
- Password: `password123`

### Creating a New Account

1. Click **"Sign Up"** in the navigation
2. Fill in the registration form:
   - Full Name
   - Email Address
   - Phone Number
   - Password (minimum 6 characters)
   - Address (optional)
   - City (optional)
3. Click **"Create Account"**
4. You'll be redirected to login page

### Browsing Services

1. On the homepage, view featured service providers
2. Click on any category card to filter services
3. Use the search bar to find specific services
4. Click **"Services"** in navigation to browse all providers

### Booking a Service

1. **Login Required**: You must be logged in to book services
2. Browse and select a service provider
3. Click **"View Details"** to see full profile
4. Click **"Book Now"**
5. Fill in booking details:
   - Booking Type (Instant/Scheduled)
   - Date & Time
   - Duration (hours)
   - Service Address
   - Special Requirements
6. Review the booking summary
7. Click **"Confirm Booking"**
8. View your booking in **"My Bookings"** page

### Managing Profile

1. Click **"Profile"** in navigation
2. Update your information:
   - Full Name
   - Phone Number
   - Address
   - City
3. Click **"Update Profile"** to save changes

## 👥 User Features

### Guest Users (Not Logged In)
- ✅ View homepage and browse categories
- ✅ Browse all service providers
- ✅ View provider details and reviews
- ✅ Search and filter services
- ❌ Cannot book services
- ❌ Cannot access bookings or profile

### Registered Users (Logged In)
- ✅ All guest features
- ✅ Book services (instant & scheduled)
- ✅ Manage bookings
- ✅ Update profile
- ✅ Write reviews (after service completion)
- ✅ View booking history

## 📸 Screenshots

### Homepage
- Hero section with search functionality
- Service categories grid
- Featured top-rated providers
- How it works section

### Services Page
- Advanced filtering (category, sort, search)
- Provider cards with ratings
- Quick booking buttons

### Service Details
- Provider profile with bio
- Skills and expertise showcase
- Customer reviews section
- Booking summary sidebar

### Booking Flow
- Comprehensive booking form
- Real-time cost calculation
- Service address input
- Booking confirmation

### User Dashboard
- My Bookings page with status tracking
- Profile management
- Booking history

## 🔧 Troubleshooting

### Issue: "Connection failed" error

**Solution:**
- Verify XAMPP MySQL is running
- Check database credentials in `config.php`
- Ensure database `intzi_db` is created

### Issue: Styles not loading

**Solution:**
- All styles are embedded in PHP files (no external CSS)
- Clear browser cache (Ctrl+F5 or Cmd+Shift+R)
- Verify file paths are correct

### Issue: Images not displaying

**Solution:**
- Create `images/providers/` folder in project root
- Add provider images or use placeholder
- Check image file names match database entries

### Issue: "Cannot modify header" warning

**Solution:**
- Ensure no output before `<?php` tags
- Check for extra spaces before/after PHP tags
- Save files with UTF-8 encoding (no BOM)

### Issue: Login/Registration not working

**Solution:**
- Verify `users` table exists in database
- Check PHP session is enabled
- Clear browser cookies and try again

### Issue: Port 80 or 3306 already in use

**Solution:**
- Stop other web servers (IIS, other Apache instances)
- Stop other MySQL instances
- Change Apache port in XAMPP config
- Change MySQL port in XAMPP config

## 🚀 Future Enhancements

- [ ] Payment gateway integration (Razorpay/Stripe)
- [ ] Real-time chat between users and providers
- [ ] Mobile application (React Native/Flutter)
- [ ] Email notifications for bookings
- [ ] SMS notifications
- [ ] Advanced analytics dashboard
- [ ] Provider registration and onboarding
- [ ] Multi-language support
- [ ] Admin panel for platform management
- [ ] Ratings and badges system
- [ ] Promotional offers and discounts
- [ ] Geolocation-based provider suggestions

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PHP PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Test thoroughly before submitting

## 📝 License

This project is licensed under the MIT License - feel free to use it for personal or commercial projects.

## 👨‍💻 Developer Information

**Project**: Intzi - Hyperlocal Service Platform  
**Version**: 1.0.0  
**Created**: November 2025  
**Technology Stack**: PHP, MySQL, HTML, CSS, JavaScript  
**Location**: Hyderabad, Telangana, India

## 📞 Support

For issues, questions, or suggestions:
- Create an issue in the repository
- Email: support@intzi.com
- Documentation: See this README

## 🙏 Acknowledgments

- Font Awesome for icons
- Google Fonts for Poppins typography
- Inspired by Urban Company and Dunzo platforms
- XAMPP for local development environment

---

**Made with ❤️ for empowering local communities**



