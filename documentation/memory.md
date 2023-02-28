# Memory

With this mode you can chain ETL

```yaml
extract:
  pull:
    type: memory
    uri: memory://your_identifier
  # This is a bit redundant, but it may be omitted in a future version
  read: 
    format: memory

transform:
  type: simple
  mapping:
    out.value: in.key
    out.key: in.value

load:
  # This is a bit redundant, but it may be omitted in a future version
  write:
    format: memory
  push:
    type: memory
    uri: memory://other_identifier
```
