# API
Image files can be queried without the web interface. This is done by either
sending a GET or POST request to <http://umls.eneman.eu/file.svg> UMLS for the
diagram is passed as a GET or POST variable.

For example does `http://umls.eneman.eu/file.svg?umls=[A]-[C][B]-[C][C]-[D][D]-[E][D]-[F]`
produce the diagram  
![umls](http://umls.eneman.eu/file.svg?umls=[A]-[C][B]-[C][C]-[D][D]-[E][D]-[F])

A checkboard background will be rendered if the checkboard get variable is set
`http://umls.eneman.eu/file.svg?umls=[A]-[C][B]-[C][C]-[D][D]-[E][D]-[F]&checkboard`  
![umls](http://umls.eneman.eu/file.svg?umls=[A]-[C][B]-[C][C]-[D][D]-[E][D]-[F]&checkboard)

# Scripting
The rendered SVG images includes CSS classes that can be used for querying or event binding.
The following CSS classes are set:

- `class` Is set on the g element surrounding the elements building the classes
- `classname` Is set on the text element containing the classname
- `attribute` Is set on the text element containing the attribute definition
- `method` Is set on the text element containing the method definition
