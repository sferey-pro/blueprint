# Project Context: Symfony Blueprint

## Overview

This is a modern Symfony 7.3 application built with PHP 8.4. It serves as a blueprint for new projects, showcasing a robust and clean architecture. The application is designed to run with FrankenPHP.

## Architecture

The project strictly follows **Domain-Driven Design (DDD)** principles and a **CQRS (Command and Query Responsibility Segregation)** pattern.

*   **Bounded Contexts**: The core business logic is organized into Bounded Contexts located under `src/Business/Contexts/`.
    *   The primary example is the `Greeting` context.

*   **Layered Architecture**: Each Bounded Context has a clear, layered structure:
    1.  **`Domain`**: Contains the pure business logic, including the `Greeting` aggregate, value objects, domain events, and the `GreetingRepositoryInterface`. It has no external dependencies.
    2.  **`Application`**: Orchestrates the domain logic. It contains `Command` and `Query` objects, implementing the CQRS pattern.
    3.  **`Infrastructure`**: Contains all the technical implementation details, such as:
        *   Symfony `Controller`s.
        *   `Doctrine` repository implementations (e.g., `DoctrineGreetingRepository`).
        *   Context-specific configuration like `routes.yaml`.

*   **Shared Kernel**: A `src/Kernel` directory exists for code shared across different Bounded Contexts.

## Key Technologies & Dependencies

*   **Backend**: Symfony 7.3, PHP 8.4
*   **Database**: Doctrine ORM, Doctrine Migrations
*   **Frontend**:
    *   Twig for server-side rendering.
    *   Symfony UX, including **React Components** and **Live Components** for an interactive UI.
    *   **Tailwind CSS** for styling.
*   **Asynchronous Operations**: Symfony Messenger is installed.
*   **Web Server**: FrankenPHP.

## Core Functionality (Example)

The `Greeting` context provides the following features:

*   **`/greetings`**: A page that displays a list of greetings, likely rendered with React.
*   **`/api/greetings`**: A GET endpoint that returns a JSON list of all greetings.
*   **`/monitoring`**: A page displaying statistics about the greetings.

## Development & Quality Assurance

The project has a strong focus on code quality, with a comprehensive suite of tools managed via [Castor](https://castor.jolicode.com/) tasks.

*   **Main Task**: `castor qa:all` runs the entire quality assurance suite (linting, static analysis, and tests).
*   **Testing**:
    *   **PHPUnit**: Run with `castor test`. Supports groups (`--group`), coverage (`--cover`), and CI reports (`--ci`).
    *   **Infection (Mutation Testing)**: Run with `castor qa:infection:run`.
*   **Static Analysis**: Run with `castor qa:analyze`.
    *   **PHPStan**: For type and error checking.
    *   **Deptrac**: To enforce architectural rules.
*   **Linting & Coding Standards**: Run with `castor qa:lint`.
    *   **PHP-CS-Fixer**: For PSR-12 coding style (`castor qa:cs:run`).
    *   **Parallel-Lint**: For fast PHP syntax checking.
    *   **Symfony Linters**: For Twig, YAML, and the container.

## Project Management & Development Tasks

A rich set of Castor tasks is available to streamline development.

*   **Application Lifecycle**:
    *   `castor symfony:start`: Starts the application services.
    *   `castor symfony:stop`: Stops the application services.
    *   `castor docker:build`: Builds or rebuilds the Docker services.
*   **Database Management**:
    *   `castor database:reload`: Drops and recreates the database, then runs migrations.
    *   `castor database:seed`: Loads data fixtures.
    *   `castor database:setup-test`: Prepares the test database.
*   **Development Tools**:
    *   `castor symfony:bash`: Opens a shell inside the PHP container.
    *   `castor log:tail`: Tails the development log file.
    *   `castor cache:clear`: Clears the Symfony application cache.
    *   `castor symfony:purge`: Removes cache, logs, and asset directories.
*   **Dependency Management**:
    *   `castor composer:install`: Installs Composer dependencies.
    *   `castor composer:update`: Updates Composer dependencies.
    *   `castor composer:outdated`: Shows outdated Composer packages.
*   **Asset Management**:
    *   `castor symfony:assets`: Builds frontend assets. Supports a `--watch` flag.
