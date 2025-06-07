# Payment Gateway Router

A Symfony-based application that routes payment transactions between multiple payment gateways using configurable traffic distribution strategies.

## Features

- Equal traffic distribution between payment gateways
- Weighted traffic distribution based on configured percentages
- Domain-Driven Design architecture
- Docker-based development environment

## Requirements

- Docker and Docker Compose

## Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/yourusername/payment-gateway-router.git
cd payment-gateway-router
```

2. Start the Docker environment:
```bash
make build
make up
```

3. Install dependencies:
```bash
make install
```

4. Run tests:
```bash
make test
```

## Usage

The application provides two main traffic splitting strategies:

### Equal Traffic Split

Distributes traffic equally among all configured gateways:

```php
$gateway1 = new Gateway1();
$gateway2 = new Gateway2();
$gateway3 = new Gateway3();
$gateway4 = new Gateway4();

$configurations = [
    new GatewayConfiguration($gateway1, 25),
    new GatewayConfiguration($gateway2, 25),
    new GatewayConfiguration($gateway3, 25),
    new GatewayConfiguration($gateway4, 25),
];

$trafficSplit = new EqualTrafficSplit($configurations);
$trafficSplit->handlePayment($payment);
```

### Weighted Traffic Split

Distributes traffic according to the specified weights:

```php
$gateway1 = new Gateway1();
$gateway2 = new Gateway2();
$gateway3 = new Gateway3();

$configurations = [
    new GatewayConfiguration($gateway1, 75),
    new GatewayConfiguration($gateway2, 10),
    new GatewayConfiguration($gateway3, 15),
];

$trafficSplit = new WeightedTrafficSplit($configurations);
$trafficSplit->handlePayment($payment);
```

## API Endpoints

- `/api/payment/equal-test` - Test the equal distribution strategy with 1000 payments
- `/api/payment/weighted-test` - Test the weighted distribution strategy with 1000 payments

## Architecture

The project follows Domain-Driven Design principles:

- **Domain Layer**: Contains the core business logic and entities
- **Application Layer**: Contains the use cases and services
- **Infrastructure Layer**: Contains implementations of interfaces defined in the domain
- **UI Layer**: Contains controllers and API endpoints
