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

Yaml is optional, it could have been a plain PHP array.

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
    type: query
    options:
      where: 'Age > :min'
      parameters:
        min: 30
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
    uri: https://webhook.site/f24c112b-8344-4fe3-a9e5-53baf36c912f
    options:
      headers:
        'Authorization': 'Basic 9e222b3b7647c7'
```

## Configuration

On the ETL part, everything is configured into one single file that describe each steps.  
But for the sake of readability, the documentation has been split, so each component has it own page.

### Extractors

Extractors are responsible to pull and read your data.

**Pull**: Responsible for pulling the data from its source, be it a local file, an API or a database.  
**Read**: Given the resource gotten in Pull, read that and convert it to a normalized array to work with later.

Most of the time, the _Pull_ and _Read_ section are independent, but for some type of resource (like database),
they work together (pull will make and handle the connection and read get the data from that connection).

<!-- TOC Extract starts -->
 - [Extract Pull: File](documentation/extract_pull_file.md)
 - [Extract Pull: Http](documentation/extract_pull_http.md)
 - [Extract Read: Csv](documentation/extract_read_csv.md)
 - [Extract Read: Json](documentation/extract_read_json.md)
 - [Extract: Database](documentation/extract_database.md)
<!-- TOC Extract ends -->

### Transformers

Transformers are responsible to filter and transform the given data.

**Filter**: Given the data, apply specific filtering logic.  
**Mapping**: Given the data, associate output fields to input fields, with optional transformation.

<!-- TOC Transform starts -->
 - [Transform Mapping: Expressive](documentation/transform_mapping_expressive.md)
 - [Transform Mapping: Simple](documentation/transform_mapping_simple.md)
 - [Transform Filter: Query](documentation/transform_filter_query.md)
<!-- TOC Transform ends -->

#### Chaining

Sometimes you may want to take advantage of multiple transformers,
you can do it by adding multiples entries into a `transformers` section.

```yaml
transformers:
  first_transform: # This name does not matter, but it has to be unique
    mapping:
      type: simple
      map:
        out.name: in.Name
        out.sex: in.Sex
        out.size: in.Height

  second_transform: # This name does not matter, but it has to be unique
    filter:
      type: query
      options:
        where: 'size > :size'
        parameters:
          size: 2
    mapping:
      type: expressive
      map:
        out.name: in.name
        out.sex: in.sex
        out.squared: 'in.size * in.size'
```

### Loaders

Loaders are responsible to write and push your data.

**Write**: Given the data, write and convert it to the specified format / type.  
**Push**: Given the resource gotten in Write, push it to the configured source.

Most of the time, the _Write_ and _Push_ section are independent, but for some type of resource (like database),
they work together (write prepare the data and push will handle the connection).

<!-- TOC Load starts -->
 - [Load Write: Twig](documentation/load_write_twig.md)
 - [Load Write: Json](documentation/load_write_json.md)
 - [Load: Database](documentation/load_database.md)
 - [Load Push: Http](documentation/load_push_http.md)
 - [Load Push: File](documentation/load_push_file.md)
<!-- TOC Load ends -->

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
        // Yaml is optional, you can provide an array instead
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

...

### Implement Custom Transformers

...

### Implement Custom Loaders

...

# Development Environment 

## Install the project

...

## Run the project

...

## Tests and code style

...

## Licence

MIT

## Attributions

`data_in.csv` comes from: https://people.sc.fsu.edu/~jburkardt/data/csv/csv.html
