<?php

declare(strict_types=1);

namespace App\Business\Shared\Utility;

/**
 * Représente le résultat d'une opération qui peut soit réussir (success)
 * soit échouer (failure). Il rend la gestion d'erreur explicite.
 *
 * @template TValue Le type de la valeur en cas de succès.
 * @template TError of \Throwable
 */
final class Result
{
    /**
     * @param TValue|null $value la valeur en cas de succès
     * @param TError|null $error L'erreur en cas d'échec
     */
    private function __construct(
        private(set) ?object $value,
        private(set) ?\Throwable $error,
    ) {
    }

    /**
     * Crée un résultat de succès.
     *
     * @template TSuccessValue
     *
     * @param TSuccessValue $value
     *
     * @return self<TSuccessValue, never>
     */
    public static function success(object $value): self
    {
        return new self($value, null);
    }

    /**
     * Crée un résultat d'échec.
     *
     * @template TFailureValue of \Throwable
     *
     * @param TFailureValue $error
     *
     * @return self<never, TFailureValue>
     */
    public static function failure(\Throwable $error): self
    {
        return new self(null, $error);
    }

    /**
     * @throws \LogicException si on essaie d'accéder à la valeur d'un résultat en échec
     *
     * @return TValue
     */
    public function value(): object
    {
        if ($this->isFailure()) {
            throw new \LogicException('Cannot get value from a failure result.');
        }

        return $this->value;
    }

    /**
     * Retourne l'erreur d'un résultat en échec.
     *
     * @throws \LogicException si on essaie d'accéder à l'erreur d'un résultat en succès
     *
     * @return TError
     */
    public function error(): \Throwable
    {
        if ($this->isSuccess()) {
            throw new \LogicException('Cannot get error from a success result.');
        }

        return $this->error;
    }

    /**
     * @throws TError
     *
     * @return TValue
     */
    public function valueOrThrow(): object
    {
        if ($this->isFailure()) {
            throw $this->error;
        }

        return $this->value;
    }

    /**
     * @template U
     *
     * @param callable(TValue): U $fn
     *
     * @return self<U, TError>
     */
    public function map(callable $fn): self
    {
        if ($this->isFailure()) {
            return self::failure($this->error);
        }

        return self::success($fn($this->value));
    }

    /**
     * @template U
     *
     * @param callable(TValue): self<U, TError> $fn
     *
     * @return self<U, TError>
     */
    public function flatMap(callable $fn): self
    {
        if ($this->isFailure()) {
            return self::failure($this->error);
        }

        return $fn($this->value);
    }

    /**
     * @param TValue $default
     *
     * @return TValue
     */
    public function valueOr(mixed $default): object
    {
        return $this->isSuccess() ? $this->value : $default;
    }

    public function isSuccess(): bool
    {
        return null === $this->error;
    }

    public function isFailure(): bool
    {
        return null !== $this->error;
    }
}
