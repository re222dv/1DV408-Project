# CI
Travis is used as CI and will run the tests on push, you can find it on
<https://travis-ci.org/re222dv/1DV408-Project>

# Location Constants
Locations in _GET, _POST, _SESSION or variables that are sent to the template should be
specified in a named constant at the top of the class so that it's possible to see
communications just by a quick overview.

Use a prefix for the specific use case
- `GV_` for a GET variable
- `PV_` for a POST variable
- `RV_` for a variable than can be sent as both GET and POST
- `SV_` for a SESSION variable
- `TV_` for a template variable

When the InputDirective is used the variable is still a `TV_` as the input is registered
in the constructor and the _POST superglobal is never accessed in the class. 
