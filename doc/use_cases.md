# UC1 Create a class diagram
A user enters UMLS describing a class diagram, presses render and a class diagram
corresponding to the user input is presented.

# UC1.1 Invalid syntax
The user enters invalid code, the parser will try to handle it as good as possible and will create
a diagram from what it can find. If nothing at all is parseable a friendly error will be presented.

# UC2 Link to a diagram
UC1 + A user copies the link to the diagram that is presented below the diagram
and sends it using preferred method.

# UC3 Login
A user enters username and password to login.

# UC4 Register
A user clicks "register a new account" and registers using a username and password.

# UC5 Save the diagram
A user fulfill UC1 & UC3 in any order and then enter a name and presses save.

# UC6 Load a diagram
UC3 + the user clicks on "my diagrams" and then on the diagram she wants to load.
