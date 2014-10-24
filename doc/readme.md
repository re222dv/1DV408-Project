# Reading the documentation

## Class diagram
While the class diagram looks messy at the first I thought it's still very interesting as it
show all dependencies (it seems, I haven't found any that's missing and as it's auto generated
I don't think that it have forgotten anything).

If we take a closer look at the models (the red ones) we can follow the and see that none of the
model classes have any dependency to anything other than model classes. We can also see that no
view (the green ones) have any dependency to a class that writes to the database (the dark red ones).
Other interesting info is than only views are dependant on my Template library (the dark green ones).


## Use Cases
The Use Cases are intentionally sparse as I think the [manual tests], [test report],
[api description] and mostly the [syntax description] much better describe the application.

[manual tests]: testing.md
[test report]: test_report.md
[api description]: api.md
[syntax description]: syntax.md
