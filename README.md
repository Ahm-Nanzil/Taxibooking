# RideXpress

A modern and efficient ride-hailing platform built with Laravel.

## Features

- User authentication and authorization
- Ride booking management
- Driver management
- Real-time booking status updates
- User ratings and reviews
- Admin dashboard for monitoring bookings and drivers

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Laravel 10.x

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd ridexpress
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Configure your database in the .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ridexpress
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

## Default Admin Credentials

After running the seeders, you can login with the following admin credentials:
- Email: admin@ridexpress.com
- Password: Admin@123

## API Documentation

### Authentication

#### Register User
```
POST /api/register
{
    "name": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string",
    "phone": "string"
}
```

#### Login
```
POST /api/login
{
    "email": "string",
    "password": "string"
}
```

### Bookings

#### List Bookings
```
GET /api/bookings
Authorization: Bearer {token}
```

#### Create Booking
```
POST /api/bookings
Authorization: Bearer {token}
{
    "pickup_location": "string",
    "dropoff_location": "string",
    "pickup_time": "datetime",
    "passengers": "integer",
    "notes": "string"
}
```

#### Show Booking
```
GET /api/bookings/{id}
Authorization: Bearer {token}
```

#### Update Booking
```
PUT /api/bookings/{id}
Authorization: Bearer {token}
{
    "status": "string",
    "driver_id": "integer"
}
```

#### Cancel Booking
```
POST /api/bookings/{id}/cancel
Authorization: Bearer {token}
```

### Drivers (Admin Only)

#### List Drivers
```
GET /api/drivers
Authorization: Bearer {token}
```

#### Create Driver
```
POST /api/drivers
Authorization: Bearer {token}
{
    "name": "string",
    "email": "string",
    "phone": "string",
    "license_number": "string",
    "vehicle_number": "string",
    "vehicle_model": "string"
}
```

#### Show Driver
```
GET /api/drivers/{id}
Authorization: Bearer {token}
```

#### Update Driver
```
PUT /api/drivers/{id}
Authorization: Bearer {token}
{
    "name": "string",
    "phone": "string",
    "license_number": "string",
    "vehicle_number": "string",
    "vehicle_model": "string",
    "is_active": "boolean"
}
```

#### Delete Driver
```
DELETE /api/drivers/{id}
Authorization: Bearer {token}
```

## Testing

Run the test suite:
```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please email support@example.com or create an issue in the repository.
