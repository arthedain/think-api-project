# Think-api-project

## Getting Started

Follow the instructions below to set up and run this project.

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)


### 1.Clone this repository:
```bash
git clone https://github.com/arthedain/think-api-project.git
cd think-api-project
```


### 2. Copy `.env` File

1. Duplicate the example environment file:
    ```bash
    cp .env.example .env
    ```

2. Generate an application key:
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

3. Update `.env` with necessary configurations, such as database credentials, queue drivers, API credentials, etc.


### 3. Docker Sail Setup
1. Start the Docker containers:
    ```bash
    ./vendor/bin/sail up -d
    ```

2. Install dependencies using Composer via Docker Sail:
    ```bash
    composer install
    ```
3. Run migrations
    ```aiignore
    php artisan migrate
    ```

### 4. Running article sync command

To run console commands, use the following commands:

```bash
php artisan app:sync-articles

php artisan app:sync-articles {id}
```

### Running Pest Tests
```
./vendor/bin/pest
```
