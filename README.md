# Laravel Banking System

Welcome to the Laravel Banking System! This project is a simple banking system implemented in Laravel.

## Prerequisites

Before you begin, ensure that you have the following installed on your machine:

- PHP = 8.1
- Composer - [Install Composer](https://getcomposer.org/doc/00-intro.md#installation)
- MySQL or any other database of your choice

## Installation

1. **Clone the repository:**

    ```bash
    git clone https://github.com/methetushar/laravel-banking-system.git
    ```

2. **Navigate to the project folder:**

    ```bash
    cd laravel-banking-system
    ```

3. **Install dependencies:**

    ```bash
    composer install
    ```

4. **Copy the environment file:**

    ```bash
    cp .env.example .env
    ```

5. **Generate application key:**

    ```bash
    php artisan key:generate
    ```

6. **Configure the database:**

    - Open the `.env` file and set the database connection details.

7. **Run database migrations:**

    ```bash
    php artisan migrate
    ```

8. **Serve the application:**

    ```bash
    php artisan serve
    ```

    The application will be accessible at `http://localhost:8000`.

## Usage

- Visit the application in your browser.
- Register as an Individual or Business user.
- Use the provided routes for deposit and withdrawal operations.

## Contributing

If you'd like to contribute to this project, please follow the [Contributing Guidelines](CONTRIBUTING.md).

## License

This project is open-sourced under the [MIT License](LICENSE).
