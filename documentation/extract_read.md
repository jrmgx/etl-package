# Extract: Read

## CSV

```yaml
extract:
  read:
    format: csv
    options:
      separator: ","
      enclosure: '"'
      trim: true
      header: true
      # with_header is an array to override or specify the header for that CSV
      with_header: ["Name", "Sex", "Age", "Height", "Weight"]
```

## JSON

```yaml
extract:
  read:
    format: json
    options:
      associative: true
```
