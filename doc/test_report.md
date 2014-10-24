# Test Rapport

## Automatic Test suite
The automatic test suite passes <https://travis-ci.org/re222dv/1DV408-Project/builds/38910033>

## Validation
The HTML and SVG validates. The CSS does not validate because of vendor prefixes, and the
validator not recognizing the css calc function. The Font Awesome CSS have long bunch off
validation errors.

## Diagrams
The diagram all look like the reference images

## Manual testing
### Registration tests
1. Make sure that you can not and get friendly errors for
  - Creating a user with too short (less than three characters) or without a username
    - `<empty>` Pass
    - `ad` Pass
  - Creating a user with too long (more than 20 characters) username
    - `A very, very long username` Pass
  - Creating a user without a password
    - Pass
  - Creating a user when the password confirmation doesn't match
    - `Password`, `Another Password` Pass
  - Creating a user with an existing username (for example Test)
    - `Test` Pass
1. Make sure that you are logged in after registration.
  - `Username`, `Password`, `Password` Pass
1. Make sure that you remain logged in after a browser restart
  - Fail, the user does not remain logged in

### Logout tests
1. Make sure that you can log out
  - Pass
1. Make sure that you remain logged out after a browser restart
  - Pass

### Login tests
1. Make sure that you can log in with your registered user
  - Pass
1. Make sure that you remain logged in after a browser restart
  - Pass

### Creating diagram tests
#### Logged in
1. Multiple classes
 - `[User][Post][Thread]` Pass
1. Class extension (that you can define a class and then add methods or attributes to it)
 - `[User|firstname][User|lastname][User|login()]` Pass
1. Associations:
  1. Previously defined classed
    - `[User|firstname][Post|text][User|lastname]-[Post]` Pass
  1. Not previously defined classes
    - `[User|lastname]-[Post]` Pass
  1. Named (although the name won't show for now)
    - `[User|lastname]-writes-[Post]` Pass
1. Attributes
  1. With type
    - `[User|firstname : string;lastname : string]` Pass
  1. Without type
    - `[User|firstname;lastname]` Pass
1. Methods
  1. With parameters
    - `[User||login(username:string, password);logout(forgetUser:bool)]` Pass
  1. Without parameters
    - `[User||login();logout()]` Pass
  1. With return type
    - `[User||login(username:string, password):bool;logout(forgetUser:bool):int]` Pass
#### Logged out
1. Multiple classes
 - `[User][Post][Thread]` Pass
1. Class extension (that you can define a class and then add methods or attributes to it)
 - `[User|firstname][User|lastname][User|login()]` Pass
1. Associations:
  1. Previously defined classed
    - `[User|firstname][Post|text][User|lastname]-[Post]` Pass
  1. Not previously defined classes
    - `[User|lastname]-[Post]` Pass
  1. Named (although the name won't show for now)
    - `[User|lastname]-writes-[Post]` Pass
1. Attributes
  1. With type
    - `[User|firstname : string;lastname : string]` Pass
  1. Without type
    - `[User|firstname;lastname]` Pass
1. Methods
  1. With parameters
    - `[User||login(username:string, password);logout(forgetUser:bool)]` Pass
  1. Without parameters
    - `[User||login();logout()]` Pass
  1. With return type
    - `[User||login(username:string, password):bool;logout(forgetUser:bool):int]` Pass
    
### Link to diagram tests
The link when editing diagrams work both when logged in and logged out
All links on MyDiagrams page work

### Saving a diagram tests
1. Make sure that you can not save a diagram with a name that is:
  1. Too short (less than three characters)
    - `<empty>` Pass
    - `mm` Pass
  1. Too long (more than 40 characters)
    - `An extremely long name that is slightly longer than forty characters` Pass
1. Make sure that you can create a new diagram and save it
  - `Blog`, `[User]-[Post][User]-[Comment][Post]-[Comment]` Pass
1. Make sure that it shows up on the "My Diagrams" page
  - Pass
1. Log out and login as another user
1. Make sure the diagram don't show up on this users "My Diagrams" page
  - Pass

### Modify a diagram tests
1. Test that you can save a modified diagram
 - Pass
1. Make sure the image on "My Diagram" is updated
 - Pass
1. Make sure the change is present after a refresh
 - Pass
1. Make sure that you can change the name of a diagram
 - Pass
1. Make sure the name on "My Diagram" is updated
 - Pass
1. Make sure the change is present after a refresh
 - Pass
1. Make sure you can delete the diagram
 - Pass
1. Make sure the diagram is not present after a refresh
 - Pass
