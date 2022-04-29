# Computed field

Add readonly field (only for display) to nova form, that is live reloading when source fields change. 

## Example

```php
use Upline\ComputedField;

ComputedField::make('Total')
    ->setComputedFunction(function ($data) {
        return $data['price'] * $data['quantity'];
    })
    ->setSourceFields(['price', 'quantity'])
```

### Explanation

`->setSourceFields($fields)` method sets fields that will be watched for changes, and will be sent to compute the field value.

`->setComputedFunction(...)` method sets a function that will be called to calculate a value of the field.


### To be done

* Add image/video of component to readme
* Test on nova 4
* Add tests
* Add details and/or index view
* Discover ability to use laravel dynamic attributes functions