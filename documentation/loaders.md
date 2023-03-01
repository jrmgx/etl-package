# Loaders

Loaders are responsible to write and push your data.

They are split in two sections: _Write_ and _Push_.

Most of the time, the _Write_ and _Push_ section are independent, but for some type of resource (like database),
they work together (write prepare the data and push will handle the connection).  
In this case the documentation will be found in the Push section.

## Write

Given the data, write and convert it to the specified format / type.

see [Load: Write](load_write.md)

## Push

Given the resource gotten in Write, push it to the configured source.

see [Load: Push](load_push.md)
