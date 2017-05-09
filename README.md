# _Timeclock_

This is a time tracking component for Joomla 3.1+.

## Version Notes
 - Version 0.9.1rc4 is the first version that works correctly with Joomla 3.7.0.
   If you upgrade to version 3.7.0, you will need to update the timeclock also.

## Project Setup

### Directory Structure
This project is broken up into the following directories:

- The com_timeclock/ directory contains the component
- The mod_timeclockinfo/ directory contains the info module
- the plg_user_timeclock/ directory contains the user setup plugin

### Requirements
#### Installation
- PHP 5.3+
- Joomla 3.1+

#### Development
- phpunit
- GNU Make

### Setup

Once packaged, this can be directly installed in Joomla.  Otherwise extreme measures
must be used to get this working in a test setup.

## Testing
This project uses phpunit to do unit testing.

_The unit testing for this is currently broken, badly_

## Deploying

The command to create the package is:

 $ make package

This gets put into tarballs, all ready for installation in Joomla.

## Troubleshooting & Useful Tools



## Contributing changes

Changes can be contributed by either:

1. Using git to create patches and emailing them to patches@hugllc.com
2. Creating another github repository to make your changes to and submitting pull requests.

## Filing Bug Reports
Please file bugs here on github.

## License
This is released under the GNU GPL V3.  You can find the complete text in the
LICENSE file, or at http://opensource.org/licenses/gpl-3.0.html
