# Transform: Mapping

## Simple

Map a given input field (in.*) to the wanted output field (out.*)

```yaml
transform:
  mapping:
    type: simple
    map:
      out.foo: in.bar
```


## Expressive

Based on the [Expression Language Component](https://symfony.com/doc/current/components/expression_language.html)

```yaml
transform:
  mapping:
    type: expressive
    map:
      out.foo: 'in.bar * 2'
```
