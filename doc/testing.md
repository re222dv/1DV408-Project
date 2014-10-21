# Manual testing
Except for the automatic test suite there is a bunch of manual tests that have to pass as well.
They are divided in two parts, the first part validating the rendered diagrams doesn't have any
exact output and have to be decided manually if the output is correct or not.

## Checking the diagrams
Login with the username `Test` and password `tester` and go through the saved diagrams
to make sure the look correct and doesn't have regressed.

The current output for the diagrams are:
### Test Example
![Test Example]()
### Test Associations
![Test Associations]()
### Test Loop 1
![Test Loop 1]()
### Test Loop 2
![Test Loop 2]()
### Test MVC
![Test MVC]()

## Checking the application
### Test register
1. Make sure that you can not and get friendly errors for
  - Creating a user with too short (less than the characters) or without a username
  - Creating a user without a password
  - Creating a user when the password confirmation doesn't match
  - Creating a user with an existing username (for example Test)
1. Make sure that you are logged in after registration.
1. Make sure that you remain logged in after a browser restart

### Test logout
1. Make sure that you can log out
1. Make sure that you remain logged out after a browser restart

### Test login
1. Make sure that you can log in with your registered user
1. Make sure that you remain logged in after a browser restart

### Test creating a diagram
