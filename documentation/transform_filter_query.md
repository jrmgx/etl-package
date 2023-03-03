# Transform Filter: Query

SQL like based query engine

<!-- config starts -->
```yaml
options:
    select:               []

    # Write prepared SQL statements with placeholder: i.e. "size > :size"
    where:                ~

    # Associate placeholders from the "where" part with the value you want: i.e. "{ size: 10 }"
    parameters:           []

```
<!-- config ends -->
