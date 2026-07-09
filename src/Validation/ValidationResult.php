<?php

declare(strict_types=1);

namespace Elqora\Chart\Validation;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class ValidationResult implements ArraySerializable
{
    /**
     * @param list<ValidationIssue> $issues
     */
    public function __construct(
        public array $issues = [],
    ) {
    }

    public static function valid(): self
    {
        return new self();
    }

    public function isValid(): bool
    {
        return $this->issues === [];
    }

    public function withIssue(ValidationIssue $issue): self
    {
        return new self([...$this->issues, $issue]);
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        return array_map(static fn (ValidationIssue $issue): string => $issue->code, $this->issues);
    }

    public function toArray(): array
    {
        return [
            'valid' => $this->isValid(),
            'issues' => SerializableValue::normalize($this->issues),
        ];
    }
}
