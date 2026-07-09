<?php

declare(strict_types=1);

namespace Elqora\Chart\Validation;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class ValidationIssue implements ArraySerializable
{
    /**
     * @param array<string, mixed> $details
     */
    public function __construct(
        public string $code,
        public string $message,
        public ?string $path = null,
        public array $details = [],
    ) {
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'code' => $this->code,
            'message' => $this->message,
            'path' => $this->path,
            'details' => $this->details === [] ? null : $this->details,
        ]);
    }
}
