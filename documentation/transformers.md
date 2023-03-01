# Transformers 

Transformers are responsible to filter and transform the given data.

They are split in two sections: _Filter_ and _Mapping_.

## Filter

Given the data, apply specific filtering logic.

see [Transform: Filter](transform_filter.md)

## Mapping

Given the data, associate output fields to input fields, with optional transformation.

see [Transform: Mapping](transform_mapping.md)

## Chaining

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
