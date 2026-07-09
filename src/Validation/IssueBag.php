<?php

declare(strict_types=1);

namespace Elqora\Chart\Validation;

final class IssueBag
{
    /** @var list<ValidationIssue> */
    private array $issues = [];

    /**
     * @param array<string, mixed> $details
     */
    public function add(string $code, string $message, ?string $path = null, array $details = []): void
    {
        $this->issues[] = new ValidationIssue($code, $message, $path, $details);
    }

    public function result(): ValidationResult
    {
        return new ValidationResult($this->issues);
    }
}
