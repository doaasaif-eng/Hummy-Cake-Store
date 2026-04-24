📝 Hummy Cake - E-Commerce Management System
🌟 Project Overview
Hummy Cake is a comprehensive web-based platform designed to digitize and manage the operations of a confectionery store. The project bridges the gap between traditional manual ordering and modern digital management, providing a seamless experience for customers and a robust control panel for administrators.

The system manages the entire lifecycle of an order, from browsing the interactive menu to final delivery, ensuring data integrity and operational efficiency.

🚀 Key Features
Interactive Customer Interface: Browse categories (Cakes, Pastries, Juices) and manage a real-time shopping cart.

Dynamic Admin Dashboard: Full CRUD operations for products, categories, and user management.

Order Tracking System: Real-time status updates (Pending, Preparing, Out for Delivery, Delivered).

Automated Calculations: Instant tax and total price calculations within the cart.

System Monitoring: An integrated Python-based script to monitor website uptime and database connectivity.

🛠️ Tech Stack
Frontend: HTML5, CSS3, JavaScript, Bootstrap.

Backend: PHP (Server-side logic).

Database: MySQL (Relational database management).

Automation: Python (For system health monitoring).

📂 System Architecture
The project follows the Waterfall Model methodology:

Requirement Analysis: Identifying the pain points of manual store management.

System Design: Creating DFDs, Use Case diagrams, and ERD (Entity Relationship Diagram).

Implementation: Developing the core modules using PHP and MySQL.

Testing: Ensuring a bug-free experience for all user roles.

🗄️ Database Structure
The database (sweet.sql) consists of several interconnected tables:

users: Handles authentication and roles (Admin, Staff, Customer).

menuitem: Stores product details, images, and availability.

orders: Tracks transactions and delivery assignments.

cart: Manages temporary session-based shopping activities.

🔧 Automation Script (System Monitor)
Included in the repository is a Python script that ensures high availability by:

Checking the HTTP response of the website.

Verifying the MySQL database connection status.

Reporting errors instantly to the administrator.
