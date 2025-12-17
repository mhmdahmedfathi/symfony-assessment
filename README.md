# Louvels.dev Symfony Assessment

This repository contains the solution for the Symfony assessment, including a Country Data Service with CRUD operations and synchronization capabilities.

## 1. Setup & Installation

### Prerequisites

- Docker (or Docker Desktop) installed.

### Installation

1. Start the Docker containers:
   ```bash
   docker-compose up --build -d
   ```
2. Install PHP dependencies:
   ```bash
   docker-compose exec -T php composer install
   ```
3. Run Database Migrations:
   ```bash
   docker-compose exec -T php bin/console doctrine:migrations:migrate --no-interaction
   ```

## 2. Usage

### Synchronization

Fetch country data from [REST Countries](https://restcountries.com) and populate the database:

```bash
docker-compose exec -T php bin/console app:country:sync
```

_Note: This command will create new records, update existing ones, and remove countries that no longer exist in the source API._

### API Documentation

You can explore the API endpoints using the Swagger UI:

- **URL**: [http://localhost:8084/api/doc](http://localhost:8084/api/doc)

### API Endpoints

The base URL for all endpoints is `http://localhost:8084`.

| Method   | Endpoint                 | Description          | Auth Required    |
| -------- | ------------------------ | -------------------- | ---------------- |
| `GET`    | `/api/v1/countries`      | List all countries   | None (Public)    |
| `POST`   | `/api/v1/countries`      | Create a new country | Yes (Basic Auth) |
| `PATCH`  | `/api/v1/countries/{id}` | Update a country     | Yes (Basic Auth) |
| `DELETE` | `/api/v1/countries/{id}` | Delete a country     | Yes (Basic Auth) |

### Security

Write operations (`POST`, `PATCH`, `DELETE`) are secured using HTTP Basic Authentication.

- **Username**: `admin`
- **Password**: `password`

## 3. Testing

### Unit Tests

Run the PHPUnit tests to verify the Entity and Service logic:

```bash
docker-compose exec -T php bin/phpunit
```

### Manual Verification

You can use `curl` to verify the endpoints manually:

**List Countries:**

```bash
curl -I http://localhost:8084/api/v1/countries
```

**Create Country (Admin):**

```bash
curl -v -X POST -u admin:password -H "Content-Type: application/json" -d '{"uuid":"TEST", "name":"Test Country", "region":"Test", "subRegion":"Test", "demonym":"Test", "population":100, "independant":true, "flag":"flag.svg", "currency":{"name":"Euro","symbol":"€"}}' http://localhost:8084/api/v1/countries
```

## 4. Key Implementation Details

- **Architecture**: Follows Symfony best practices with Entity, Repository, Service, and Controller layers.
- **Validation**: Uses Symfony Validator constraints (`#[Assert\NotBlank]`, etc.) to ensure data integrity.
- **Documentation**: Uses `nelmio/api-doc-bundle` for OpenAPI/Swagger documentation.
- **Testing**: Includes PHPUnit tests for core logic.
