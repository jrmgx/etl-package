# Extract: Database

Based on [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html)

Every PDO drivers are supported, use these identifiers in your `uri`:
 - `mysql` for MySQL
 - `sqlite` for SQLite
 - `pgsql` for PostgreSQL
 - `oci` for Oracle
 - `sqlsrv` for Microsoft SQL Server

[More info](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/configuration.html#driver)

examples:
 - `mysql://user:password@host:port/database_name`
 - `sqlite://./data/database_in.sqlite` (relative path)
 - `sqlite:////home/data/database_in.sqlite` (absolute path)

<!-- config starts -->
```yaml
options:
    select:               []
    from:                 ~ # Required

    # Prepared SQL statements with placeholder: i.e. "size > :size"
    where:                null

    # Associate placeholders from the "where" part with the value you want: i.e. "{ size: 10 }"
    parameters:           []

```
<!-- config ends -->
