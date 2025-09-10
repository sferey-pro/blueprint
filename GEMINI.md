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

The project has a strong focus on code quality, with several tools and scripts configured:

*   **Testing**: PHPUnit, with different test suites (`unit`, `integration`, `functional`, `e2e`).
*   **Static Analysis**: PHPStan.
*   **Coding Standards**: PHP-CS-Fixer (PSR-12).
*   **Composer Scripts**:
    *   `composer lint`: Checks code style and syntax.
    *   `composer analyze`: Runs static analysis.
    *   `composer test`: Runs the full test suite (linting, analysis, and PHPUnit tests).
