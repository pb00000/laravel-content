<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Support\Arr;

trait HasRules
{
    protected $rules;

    public function defaultRules(): array
    {
        return [];
    }

    public function getRules(): array
    {
        if (!is_array($this->rules)) {
            return $this->defaultRules();
        }

        return $this->rules;
    }

    public function addToRules($rules): self
    {
        return $this->setRules(array_merge($this->getRules(), Arr::wrap($rules)));
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }
}
