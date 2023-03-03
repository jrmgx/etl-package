# Extract: Database

Based on https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html

<!-- config starts -->
```yaml
options:
    select:               []
    from:                 ~

    # Write prepared SQL statements with placeholder: i.e. "size > :size"
    where:                ~

    # Associate placeholders from the "where" part with the value you want: i.e. "{ size: 10 }"
    parameters:           []

```
<!-- config ends -->
