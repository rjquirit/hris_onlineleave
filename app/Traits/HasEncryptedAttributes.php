<?php

namespace App\Traits;

use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Model;

trait HasEncryptedAttributes
{
    /**
     * The encryption service instance.
     *
     * @var EncryptionService
     */
    protected static $encryptionService;

    /**
     * Boot the trait.
     */
    protected static function bootHasEncryptedAttributes()
    {
        static::$encryptionService = app(EncryptionService::class);

        // Encrypt attributes before saving
        static::saving(function (Model $model) {
            $model->encryptAttributes();
        });

        // Decrypt attributes after retrieving
        static::retrieved(function (Model $model) {
            $model->decryptAttributes();
        });
    }

    /**
     * Get the list of encrypted attributes for this model.
     */
    abstract protected function getEncryptedAttributes(): array;

    /**
     * Encrypt the specified attributes before saving.
     */
    protected function encryptAttributes(): void
    {
        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute])) {
                $originalValue = $this->attributes[$attribute];

                // Skip if already encrypted (check if it's a base64 string)
                if ($this->isEncrypted($originalValue)) {
                    continue;
                }

                // Encrypt the value
                $encryptedValue = static::$encryptionService->encrypt($originalValue);
                $this->attributes[$attribute] = $encryptedValue;

                // Generate blind index for searchable fields
                $searchIndexColumn = $attribute.'_search_index';
                if ($this->hasSearchIndex($attribute)) {
                    $blindIndex = static::$encryptionService->generateBlindIndex($originalValue);
                    $this->attributes[$searchIndexColumn] = $blindIndex;
                }
            }
        }
    }

    /**
     * Decrypt the specified attributes after retrieving.
     */
    protected function decryptAttributes(): void
    {
        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute]) && $this->isEncrypted($this->attributes[$attribute])) {
                $this->attributes[$attribute] = static::$encryptionService->decrypt($this->attributes[$attribute]);
            }
        }
    }

    /**
     * Check if a value is encrypted (basic check for base64 encoded string).
     *
     * @param  mixed  $value
     */
    protected function isEncrypted($value): bool
    {
        if (! is_string($value) || empty($value)) {
            return false;
        }

        // Check if it's a valid base64 string and has reasonable length for encrypted data
        $decoded = base64_decode($value, true);

        return $decoded !== false && base64_encode($decoded) === $value && strlen($value) > 20;
    }

    /**
     * Check if an attribute should have a search index.
     */
    protected function hasSearchIndex(string $attribute): bool
    {
        $tableName = $this->getTable();
        $searchableFields = config("encryption.searchable_fields.{$tableName}", []);

        return in_array($attribute, $searchableFields);
    }

    /**
     * Get an attribute value with automatic decryption.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        // If this is an encrypted attribute and it's encrypted, decrypt it
        if (in_array($key, $this->getEncryptedAttributes()) && $this->isEncrypted($value)) {
            return static::$encryptionService->decrypt($value);
        }

        return $value;
    }

    /**
     * Set an attribute value with automatic encryption preparation.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // Store the original value; encryption will happen in the saving event
        return parent::setAttribute($key, $value);
    }

    /**
     * Scope a query to search encrypted fields using blind indexes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchEncrypted($query, string $field, string $searchTerm)
    {
        if (! in_array($field, $this->getEncryptedAttributes())) {
            // If not an encrypted field, use normal search
            return $query->where($field, 'LIKE', "%{$searchTerm}%");
        }

        $searchIndexColumn = $field.'_search_index';
        $blindIndex = static::$encryptionService->generateBlindIndex($searchTerm);

        return $query->where($searchIndexColumn, $blindIndex);
    }

    /**
     * Scope a query to search encrypted fields with partial matching (LIKE).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchEncryptedLike($query, string $field, string $searchTerm)
    {
        if (! in_array($field, $this->getEncryptedAttributes())) {
            // If not an encrypted field, use normal search
            return $query->where($field, 'LIKE', "%{$searchTerm}%");
        }

        // For LIKE searches, we need to search by the blind index of the search term
        // This works for exact matches and beginning-of-string matches
        $searchIndexColumn = $field.'_search_index';
        $blindIndex = static::$encryptionService->generateBlindIndex($searchTerm);

        return $query->where($searchIndexColumn, $blindIndex);
    }
}
