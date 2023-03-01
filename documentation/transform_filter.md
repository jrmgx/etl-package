# Transform: Filter

## Query

SQL like based query engine

```yaml
transform:
  filter:
    type: query
    options:
      select: ['name', 'age', 'data']
      where: 'size > :size'
      parameters:
        size: 1
```
