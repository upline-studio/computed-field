<?php

namespace Upline\ComputedField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class ComputedField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'computed-field';

    private $computedFunction = null;

    private $sourceFields = [];

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        $this->hideFromIndex();
        $this->hideFromDetail();
        parent::__construct($name, $attribute, $resolveCallback);
    }

    public function setComputedFunction(callable $computedFunction): self
    {
        $this->computedFunction = $computedFunction;
        return $this;
    }

    public function setSourceFields(array $fields): self
    {
        $this->sourceFields = $fields;
        $this->withMeta([
            'sourceFields' => $fields
        ]);
        return $this;
    }

    public function resolve($resource, $attribute = null)
    {
        $this->withMeta(['calculateUrl' => $this->getUrl()]);
        $values = collect($this->sourceFields)->mapWithKeys(fn($sourceField) => [
            $sourceField => $this->resolveAttribute($resource, $sourceField)
        ]);
        $this->withMeta(['initialValues' => $values]);
        $this->value = call_user_func($this->computedFunction, $values);
    }

    private function getUrl()
    {
        $request = app(NovaRequest::class);
        return route('upline.computed-field.calculate', [
            'resource' => $request->route('resource'),
            'field' => $this->attribute
        ]);
    }

    /**
     * @return null
     */
    public function getComputedFunction()
    {
        return $this->computedFunction;
    }
}
