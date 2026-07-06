<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;

interface RepositoryInterface
{
    /**
     * Purpose: describes the all contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Returns all model records; needed for simple reading scenarios without filters.
     */
    public function all();

    /**
     * Purpose: describes the find contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Looks for record by primary key; used as basic secure access to the model.
     */
    public function find(int|string $id);

    /**
     * Purpose: describes the create contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Creates record from an array of attributes; A common low-level method for heirs.
     */
    public function create(array $data);

    /**
     * Purpose: describes the create from dto contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Creates record from DTO; It is necessary for record data to go through a typed contract.
     */
    public function createFromDto(DataTransferObject $dto);

    /**
     * Purpose: describes the update contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Updates record by primary key by array of attributes; returns the result of saving.
     */
    public function update(array $data, int $id, string $attribute = 'id');

    /**
     * Purpose: describes the update from dto contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Updates record by primary key data from DTO; used for typed update operations.
     */
    public function updateFromDto(int|string $id, DataTransferObject $dto): bool;

    /**
     * Purpose: describes the delete contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Delete record by primary key; returns false if record is not found.
     */
    public function delete(int|string $id): bool;

    /**
     * Purpose: describes the delete all contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Delete all model records without resetting sequence; needed to clean the table using query builder.
     */
    public function deleteAll(): void;

    /**
     * Purpose: describes the truncate contract for RepositoryInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * Completely clears the model table with counters reset where the driver supports it.
     */
    public function truncate(): void;
}
