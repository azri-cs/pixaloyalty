# PixaLoyalty

A simplified loyalty points management system using FilamentPHP and the TALL stack.

## Assumptions & Implementation Choices
###
- Each user have many brands and each brand have many users (multiple managers, franchise).
- Each user have many businesses but each business have only 1 owner.
- A customer belongs to a business and have many point transactions.

### Implementation Choices
- Soft Deletion:
  - Only critical tables (`customers` and `point_transactions`) implement soft deletion to facilitate data retrieval and maintain data integrity, avoiding unnecessary complexity in other tables.
- Transaction Type:
  - Instead of using a database enum for point_transactions.transaction_type, a string is used. This decision pushes the enum enforcement to the application layer, making it easier to migrate to new systems or databases in the future.
- Architecture:
   - By using the TALL stack, the project leverages modern, reactive UI components and a robust backend, ensuring a scalable and maintainable codebase.

## Features
1. Authentication System:
   - Secure login for business owners, including integration of brand and business details.
2. Dashboard Widget:
   - Real-time overview for business owners with key performance indicators.
3. Comprehensive management of customers with functionality to issue and deduct points.

## Future Improvements
- [ ] Attributes Based Access Control
- [ ] Allow multiple ownership of businesses
- [ ] Add purchase history tracking
- [ ] Add referral reward feature to incentivize word of mouth
- [ ] Add purchase streak (mission) or buy-x-free-1 feature based on purchase history to incentivize sale

## Pre-requisites
- [ ] PHP >= 8.2
- [ ] MySQL, MariaDB, PostgreSQL or SQLite
- [ ] Composer

## Setup

1. ```
   git clone https://github.com/azri-cs/pixaloyalty
   ```
2. ```
   cd pixaloyalty
   ```
3. ```
   cp .env.example .env && composer install && php artisan key:generate
   ```
4. Setup all the necessary details especially database credentials in the `.env`
5. ```
   php artisan migrate
   ```
   include `--seed` if you want to populate the DB.
6. ```
   php artisan serve
   ```
   and it will be available on http://127.0.0.1:8000/admin
