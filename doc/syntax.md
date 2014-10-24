# UMLS syntax
The syntax is designed to be similar to the rendered diagrams and is inspired of yUML.

## Class definition
A class is defined within brackets `[User]`.  
![A class](http://umls.eneman.eu/file.svg?umls=[User])

Attributes are specified after a pipe and separated by semicolons `[User|name;age]`.  
![A class with attributes](http://umls.eneman.eu/file.svg?umls=[User|name;age])

Types can be specified as well and separates with a colon `[User|name:String;age:int]`.  
![A class with typed attributes](http://umls.eneman.eu/file.svg?umls=[User|name:String;age:int])

Methods are specified after a second pipe `[User|name|login()]` with or without attributes `[Diagram||draw()]`.  
![Classes with a method](http://umls.eneman.eu/file.svg?umls=[User|name|login()][Diagram||draw()])

Multiple methods are separated with semicolons `[User||login();logout()]`.  
![A class with methods](http://umls.eneman.eu/file.svg?umls=[User||login();logout()])

Attributes are specified inside the parentheses and separated by comma `[User||login(username, password)]`.  
![A class with a method with attributes](http://umls.eneman.eu/file.svg?umls=[User||login(username, password)])

Types can again be specified with colon `[User||login(username:string, password)]` and return types
to `[User||login(username, password):bool]`.  
![A class with a method with types](http://umls.eneman.eu/file.svg?umls=[User||login(username:string, password):bool])

Class definitions extend each other so `[User|name;age|login()][User||logout()]`
is the same as `[User|name;age|login(username:string, password);logout()]`.  
![An extended class](http://umls.eneman.eu/file.svg?umls=[User|name;age|login()][User||logout()])

## Associations
Associations are specified by binding to classes with a hyphen `[User]-[Post]`  
![Associated classes](http://umls.eneman.eu/file.svg?umls=[User]-[Post])

## Future extensions
Associations shall be extended in the future to support names, multiplicity and other kinds
(dependencies, generalisation/specification and realization). For this to be really useful
however the placement algorithm needs to improve.
