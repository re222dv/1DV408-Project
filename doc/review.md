# Review

## Notable parts

### Template Engine
I started on the template engine in Laboratory 2 with the intention that templates should be
extremely simple, but I have had to extend them during the project. With this knowledge I would
never have written a template engine myself and instead just have used PHP with output buffer
management, which would make a fair bunch of my code unnecessary.

### Placement Algorithm
The placement algorithm code is very complex and does a pretty bad job, however I don't have
the knowledge to make a better one. I'm very satisfied with the decision of writing it with traits
as it made it self contained with only logic handling code. However instead of the array functions
I would have preferred a NodeList class.

### Links
I wanted to have the urls define the state of the application as I think being able to pass the
link of the current page is a great user experience. However as I do this mostly be keeping the full
umls code in the urls, they are very long and it's limiting the size of the diagrams to 16k (which
is the current max request size of my web server). I also do create link the the SVG files in two
places (InputViews template and MyDiagramsView) so a redesign of links should happen.

### Di
Di is probably the part I'm most satisfied about. The Injector implementation is very clean and it
have really helped the development.

### Database
The Database service feels like a good start but I'm not happy that WHERE queries still have to be
written.
