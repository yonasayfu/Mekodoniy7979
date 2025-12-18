# Project Mekodonia: Connecting Donors with Elders

## 1. Project Vision

To create a web application that connects donors with elders at the Mekodonia charity organization. The platform will facilitate sponsorships, manage donations, and provide a comprehensive database for the organization's staff. It aims to build personal relationships between donors and elders, fostering a sense of family and community.

## 2. User Roles

The system will have the following user roles:

*   **Guest/Public User:** Can view the landing page, see general information about the organization, view a list of elders available for sponsorship, and make one-time donations.
*   **Donor:** A registered user who has sponsored one or more elders. They can view their sponsored elders, see their history, receive notifications, manage their payment methods, and generate reports of their contributions.
*   **Elder:** Managed by the organization's staff. Their profiles, including history, photos, videos, and needs, are displayed on the platform.
*   **Staff/Admin:** Employees of the Mekodonia organization. They can manage elders, donors, sponsorships, and donations. They can also generate reports and manage the content of the website.
*   **Super Admin:** Has full control over the system, including managing user roles and permissions.

## 3. Core Features

### 3.1. Landing Page

*   Simple and intuitive design.
*   Information about the Mekodonia organization.
*   A gallery of elders available for sponsorship, with search and filter options (by branch, gender, age, etc.).
*   A section for one-time donations (for guests).
*   A sliding report showing recent sponsorships (e.g., "John Doe is now sponsoring Elder Abebe").
*   User registration and login for donors.

### 3.2. Donor Features

*   **Dashboard:** An overview of their sponsorships, recent activities, and notifications.
*   **My Elders:** A list of the elders they are sponsoring, with links to their detailed profiles.
*   **Elder Profile:** Detailed information about the elder, including their history, photos, videos, and monthly needs.
*   **Communication:** A feature to send messages or updates to the elder (moderated by the staff).
*   **Payment Management:** Donors can add and update their payment methods (bank transfer details, etc.).
*   **Donation History:** A record of all their donations.
*   **Notifications:** Receive email, SMS, or in-app notifications for payment reminders, updates from the organization, etc.
*   **Annual Reports:** "Thank You" reports summarizing their impact.

### 3.3. Staff/Admin Features

*   **Dashboard:** An overview of the organization's key metrics (number of donors, elders, sponsorships, total donations, etc.).
*   **Elder Management:** Create, update, and manage elder profiles. Set their priority and needs.
*   **Donor Management:** View and manage donor information.
*   **Sponsorship Management:** View and manage all sponsorships.
*   **Donation Management:** Track and verify all donations.
*   **Branch Management:** Manage the different branches of the organization.
*   **Reporting:** Generate various reports (e.g., financial reports, donor reports, elder reports).
*   **Content Management:** Manage the content of the landing page and other informational pages.

### 3.4. One-Time Donations

*   A simple form on the landing page for guests to make a one-time donation.
*   Integration with popular Ethiopian payment gateways (e.g., Telebirr, CBE Birr) for easy mobile transfers.
*   Option to donate for a specific purpose (e.g., "a meal for an elder").

## 4. Technology Stack

*   **Backend:** Laravel 12
*   **Frontend:** Vue.js 3
*   **Database:** MySQL
*   **Other:** Inertia.js to connect the backend and frontend.

## 5. High-Level Architecture

The application will be a monolithic application built with Laravel. The frontend will be built with Vue.js and integrated into the Laravel application using Inertia.js. This architecture is simple, easy to develop and maintain, and well-suited for this type of application.

## 6. Next Steps

1.  **Database Schema Design:** Create a detailed database schema based on the features outlined above.
2.  **Development Plan:** Break down the development into smaller, manageable tasks.
3.  **Implementation:** Start the implementation of the features.