# Location Constants
Locations in _GET, _POST, _SESSION or variables that are sent to the template should be
specified in a named constant at the top of the class so that it's possible to see
communications just by a quick overview.

Use a prefix for the specific use case
`GV_` a GET variable
`PV_` a POST variable
`RV_` a variable than can be sent as both GET and POST
`SV_` a SESSION variable
`TV_` a template variable

When the InputDirective is used the variable is still a `TV_` as the input is registered
in the constructor and the _POST superglobal is never accessed in the class. 
