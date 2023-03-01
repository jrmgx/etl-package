# Extractors

Extractors are responsible to pull and read your data.

They are split in two sections: _Pull_ and _Read_.

Most of the time, the _Pull_ and _Read_ section are independent, but for some type of resource (like database),
they work together (pull will make and handle the connection and read get the data from that connection).  
In this case the documentation will be found in the Pull section.

## Pull

Responsible for pulling the data from its source, be it a local file, an API or a database.

see [Extract: Pull](extract_pull.md)

## Read

Given the resource gotten in Pull, read that and convert it to a normalized array to work with later.

see [Extract: Read](extract_read.md)
