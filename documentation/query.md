# Query

SQL like based query engine

```yaml
extract:
  ...

transform:
  filter:
    type: query
    options:
      select: ['value']
      from: key_values
      where: 'key <> :forbidden_key AND value <> :forbidden_value'
      parameters:
        forbidden_key: 'foo'
        forbidden_value: 'value'
  mapping:
    type: simple
    map:
      out.foo: in.bar

load:
  ...
```
