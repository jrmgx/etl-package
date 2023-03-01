# Query

SQL like based query engine

```yaml
extract:
  ...

transform:
  filter:
    type: query
    options:
      select: ['name', 'age', 'data']
      where: 'size > :size'
      parameters:
        size: 1
  mapping:
    type: simple
    map:
      out.foo: in.bar

load:
  ...
```
