# Load: Push

## File

```yaml
loader:
  push:
    type: file
    uri: ./data_out.json
```

## Database

Based on https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html

Load + Push combo

```yaml
load:
  write:
    format: database
    options:
      into: key_values
  push:
    type: database
    uri: pdo-sqlite://user:password@host:1234/../demo/database_out.sqlite
```

## HTTP

```yaml
load:
  push:
    type: http
    uri: http://0.0.0.0:1234/demo/index.php?route=post
    options:
      method: POST
```
