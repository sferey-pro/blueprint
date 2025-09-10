# Bounded Context: Greeting

This document summarizes the architecture and functionality of the `Greeting` Bounded Context.

## 1. Purpose

The `Greeting` context is responsible for managing the entire lifecycle of "greetings". This includes their creation, publication, and retrieval. It serves as a primary example of a full DDD/CQRS implementation within this project.

## 2. Domain Layer (`Domain/`)

This layer is the heart of the context and contains the core business logic, completely independent of any technical framework.

*   **Aggregate Root**: `Greeting.php`
    *   Represents a single greeting message.
    *   Manages its own state, including `message`, `author`, `status`, and `createdAt`.
    *   State changes are handled through business methods like `publish()`.
    *   Raises domain events (`GreetingWasCreated`, `GreetingWasPublished`) to communicate state changes.

*   **Value Objects**:
    *   `GreetingId`: A strongly-typed identifier for the `Greeting` aggregate.
    *   `Author`: Represents the creator of the greeting, encapsulating an `Email` value object.
    *   `GreetingStatus`: A PHP `enum` (`DRAFT`, `PUBLISHED`, `ARCHIVED`) that defines the explicit lifecycle of a `Greeting`.

*   **Domain Events**:
    *   `GreetingWasCreated`: Raised when a new `Greeting` is created.
    *   `GreetingWasPublished`: Raised when a `Greeting` is successfully published.

*   **Repository Interface**:
    *   `GreetingRepositoryInterface.php`: Defines the persistence contract (`add`, `ofId`). This abstraction ensures the domain layer remains decoupled from the database implementation.

## 3. Application Layer (`Application/`)

This layer orchestrates the domain logic by implementing the CQRS pattern. It does not contain any business logic itself.

*   **Commands (Write Model)**:
    *   `CreateGreetingCommand`: Intent to create a new greeting. Handled by `CreateGreetingHandler`.
    *   `PublishGreetingCommand`: Intent to publish a greeting. Handled by `PublishGreetingHandler`, which uses the Symfony Workflow component to validate the state transition.

*   **Queries (Read Model)**:
    *   `ListGreetingsQuery`: Fetches a list of greetings for display.
    *   `GetGreetingStatisticsQuery`: Fetches statistics (total, drafts, published).
    *   **Read Models (DTOs)**: `GreetingView` and `GreetingStatisticsView` are simple, read-only data transfer objects returned by queries.
    *   **Finder Interface**: `GreetingFinderInterface` defines the contract for retrieving read models.

*   **Event Listener**:
    *   `NotifyOnGreetingCreatedHandler`: Listens for the `GreetingWasCreated` event and triggers a real-time notification using **Mercure**. This is a perfect example of handling side effects.

## 4. Infrastructure Layer (`Infrastructure/`)

This layer provides the concrete implementation of the interfaces defined in the layers above and exposes the context's functionality to the outside world.

*   **Entry Points**:
    *   `Controller/`: Exposes the context's features via an HTTP API (`/api/greetings`) and a web page (`/greetings`).
    *   `Command/`: Provides CLI commands (e.g., `greeting:create`, `greeting:list`, `greeting:publish`) to interact with the application from the console.

*   **Persistence (`Persistence/Doctrine/`)**:
    *   `DoctrineGreetingRepository`: Implements both `GreetingRepositoryInterface` (for the write model) and `GreetingFinderInterface` (for the read model). It efficiently creates `GreetingView` DTOs directly from DQL queries for optimal performance.
    *   `Mapping/`: Contains Doctrine XML mapping files, decoupling the domain objects from persistence metadata.
    *   `Types/`: Includes custom Doctrine types like `GreetingIdType` to seamlessly handle custom Value Objects.
