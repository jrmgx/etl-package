# ETL Package

**THIS IS A WORK IN PROGRESS**

[ETL: Extract, Transform, Load](https://en.wikipedia.org/wiki/Extract,_transform,_load)

This package will allow you to:
- **Extract** data from a given source
- **Transform** it as you need
- **Load** the result to a given destination

It is based on a simple config file described below.

## Basic Example

In this basic example:
- We are getting a CSV file from the local repository
- We transform the data a bit
- We send back the transformed data into an API as JSON

Yaml is optional, and it could have been a plain PHP array.

```yaml
extract:
  pull:
    type: file
    uri: ./demo/data_in.csv
  read:
    format: csv
    options:
      trim: true

transform:
  filter:
    type: none
  mapping:
    type: expressive
    map:
      out.name: in.Name
      out.sex: in.Sex
      out.age_in_sec: 'in.Age * 365 * 24 * 60 * 60'

load:
  write:
    format: json
  push:
    type: http
    uri: https://example.org/api/customer
    options:
      headers:
        'Authorization': 'Basic 9e222b3b7647c7'
```

## Configuration

On the ETL part, everything is configured into one single file that describe each steps.  
But for the sake of simplicity, the documentation has been split into multiple sections.

### Extractors and Loaders

- [HTTP and API](documentation/http.md)
- [Database](documentation/database.md)
- [File (local and distant)](documentation/file.md)
- [Templates (Twig)](documentation/twig.md)
- [In Memory](documentation/memory.md)

### Transformers: Filters and Mapping

- [Simple Transformer](documentation/simple.md)
- [Expressive Transformer](documentation/expressive.md)

## Use it in your project

### Symfony or via dependency injection

In a Symfony project you can add those lines to your `services.yaml` to make it work:
```yaml
    Jrmgx\Etl\:
        resource: '../vendor/jrmgx/etl-package/src/'
        exclude:
            - '../vendor/jrmgx/etl-package/src/Config/'
```

This will register everything as a service, and then you can inject `Etl` like this:
```php
class YourService 
{
    public function __construct(private readonly Etl $etl) { }

    public function yourMethod(): void 
    {
        // Yaml is optional, you can provide a basic array instead
        $configFile = Yaml::parseFile(__DIR__.'/../../config/etl.yaml');
        $config = new Config($configFile, __DIR__ . '/../../');

        $this->etl->execute($config);
    }
}
```

In other projects using dependency injection it should be pretty similar.

### Manual setup

TODO

## Add custom Extractors/Transformers/Loaders

### Implement Custom Extractors

Explain the `customOptionsResolver`
...

### Implement Custom Transformers

...

### Implement Custom Loaders

...

# Development Environment 

## Install the project

...

## Run the project

explain `composer dump-autoload --classmap-authoritative`
...

## Tests and code style

...

## Licence

MIT

## Attributions

`data_in.csv` comes from: https://people.sc.fsu.edu/~jburkardt/data/csv/csv.html
