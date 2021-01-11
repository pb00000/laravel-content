<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

use HTMLPurifier as BasePurifier;
use HTMLPurifier_Config as BasePurifierConfig;

class HtmlPurifier implements HTMLSanitizer
{
    private $config;
    private $instance;

    public function __construct(BasePurifierConfig $config = null)
    {
        $this->config = $config ?: BasePurifierConfig::createDefault();
    }

    public function withConfig(callable $callable): self
    {
        call_user_func($callable, $this->config);

        return $this;
    }

    private function getInstance(): BasePurifier
    {
        if (!$this->instance) {
            return new BasePurifier($this->config);
        }

        return $this->instance;
    }

    public function execute($value = null): ?string
    {
        return $this->getInstance()->purify($value ?: '') ?: null;
    }
}
