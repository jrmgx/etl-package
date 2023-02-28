# Simple

Map a given input field (in.*) to the wanted output field (out.*)

```yaml
extract:
  ...

transform:
  mapping:
    type: expressive
    map:
      out.foo: in.bar

load:
  ...
```
