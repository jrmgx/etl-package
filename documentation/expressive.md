# Expressive

Based on the [Expression Language Component](https://symfony.com/doc/current/components/expression_language.html)

```yaml
extract:
  ...

transform:
  mapping:
    type: expressive
    map:
      out.foo: 'in.bar * 2'

load:
  ...
```
