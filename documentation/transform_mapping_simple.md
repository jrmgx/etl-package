# Transform Mapping: Simple

Map a given input field (in.*) to the wanted output field (out.*)

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
```yaml
options:

    # Convert multi-dimensional values to string (json encoding)
    flatten:              false

```
<!-- config ends -->
