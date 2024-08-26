# Parking Administration Application

## Table of Contents
- [Parking Administration Application](#parking-administration-application)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [Features](#features)
  - [Installation](#installation)
    - [Prerequisites](#prerequisites)
    - [Steps](#steps)
  - [Usage](#usage)
  - [Contributing](#contributing)
  - [License](#license)

## Introduction
The **Parking Administration Application** is a locally hosted web-based system designed to manage parking spaces efficiently. It provides a straightforward interface for administrators to manage categories, customers, time durations, prices, and parking slots. The application also supports user management, reporting, and profile management.

## Features
- **Category Management**: Add, edit, and manage parking categories.
- **Customer Management**: Maintain customer records and track parking details.
- **Duration Management**: Define and manage parking durations.
- **Pricing Management**: Set and modify pricing for different categories and durations.
- **Slot Management**: Manage available parking slots.
- **User Management**: Add and edit user information and roles.
- **Reports**: Generate and view reports on parking usage and revenue.

## Installation

### Prerequisites
- **Web Server**: Local server software such as XAMPP, WAMP, or MAMP.
- **PHP**: Version 7.4 or above.
- **MySQL**: Version 5.7 or above.

### Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/watermelonich/Parking-Administration-App.git
   ```
2. Move the project to your web server's root directory (e.g., `htdocs` for XAMPP):
   ```bash
   mv Parking-Administration-App /path/to/your/htdocs/
   ```
3. Import the database schema:
   - Open PHPMyAdmin or any MySQL client.
   - Create a new MySQL database.
   - Import the `config/database.sql` file into the database.

4. Update the database configuration:
   - Open `config/config.php`.
   - Set your database host, name, username, and password.

5. Access the application via your web browser:
   ``` 
   http://localhost/Parking-Administration-App/index.php
   ```

## Usage
- **Admin Dashboard**: After logging in, the admin can access the dashboard to manage various aspects of the parking system.
- **Category Management**: Add, edit, or delete parking categories.
- **Customer Management**: View and manage customer details.
- **Duration and Pricing**: Define parking time durations and associated pricing.
- **Slot Management**: Monitor and manage available parking slots.
- **Reports**: Generate reports for parking usage, revenue, and other metrics.

## Contributing
Contributions are welcome! Please fork this repository, create a new branch, and submit a pull request. For major changes, open an issue to discuss what you would like to change.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE.txt) file for details.
