# Extract: Pull

## File

```yaml
extract:
  pull:
    type: file
    uri: ./data_in.csv
```

## Database

Based on https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html

Pull + Read combo

```yaml
extract:
  pull:
    type: database
    uri: pdo-sqlite://user:password@host:1234/../demo/database_in.sqlite
  read:
    format: database
    options:
      # select: ['value']
      from: key_values
      where: 'key <> :forbidden_key AND value <> :forbidden_value'
      parameters:
        forbidden_key: 'foo'
        forbidden_value: 'value'
```

## Memory

Pull + Read combo

```yaml
extract:
  pull:
    type: memory
    uri: memory://your_identifier
  # This is a bit redundant, but it may be omitted in a future version
  read: 
    format: memory
```

## HTTP

```yaml
extract:
  pull:
    type: http
    uri: http://0.0.0.0:1234/demo/index.php?route=get
    options:
      method: GET
```
