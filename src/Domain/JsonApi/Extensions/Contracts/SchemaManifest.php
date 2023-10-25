<?php

namespace Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts;

use Dystcz\LunarApi\Domain\JsonApi\Contracts\Schema as SchemaContract;
use Illuminate\Support\Collection;

interface SchemaManifest extends Manifest
{
    /**
     * Register schemas.
     *
     * @param  Collection<string,class-string>  $schemas
     */
    public function register(Collection $schemas): void;

    /**
     * Register single schema.
     *
     * @param  class-string  $schemaClass
     */
    public function registerSchema(string $schemaClass): void;

    /**
     * Get the registered schema for a base schema class.
     */
    public function getRegisteredSchema(string $baseSchemaClass): SchemaContract;

    /**
     * Get list of registered schema types.
     */
    public function getSchemaTypes(): Collection;

    /**
     * Removes schema from manifest.
     */
    public function removeSchema(string $baseSchemaClass): void;

    /**
     * Get list of all registered models.
     */
    public function getRegisteredSchemas(): Collection;

    /** {@inheritDoc} */
    public static function for(string $class): Extension;
}
