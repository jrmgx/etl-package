# Transform Mapping: Expressive

Based on the [Expression Language Component](https://symfony.com/doc/current/components/expression_language.html)

If a field is missing from a line in the upfront data, it will default to `null`.

example:
```php
['city' => 'Paris', 'country' => 'France'],
['city' => 'New-York' /* Missing country */],

// Will result in
['city' => 'Paris', 'country' => 'France'],
['city' => 'New-York' 'country' => null],
```

<!-- config starts -->
No options for this component.
<!-- config ends -->
