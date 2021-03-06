# PHP Sandbox

Simple and usefull tool to execute custom php scripts. It allows to block specified function to increase security.

## Screenshot

<img src="http://blog.lugowski.eu/wp-content/uploads/2016/02/php_sandbox.png?v=1.1" alt="PHP Sandbox" border="0" />

## Installation

Clone that repository wherever you want (in my example `/www/php_sandbox`)

```
$ git clone git@github.com:plugowski/php_sandbox.git
$ cd php_sandbox
```

and load all dependencies via composer:


```
$ composer install
```

The final step is to run docker using docker-compose commands (of course the first step is to install docker for your environment.

```
$ docker-compose build
$ docker-compose up -d
```

Now you have working docker, go to your favourite browser and hit that addres:

```
localhost:8080
```

Voila!

## Usage

Just type some code and push `Evaluate` button or use keyboard shortcut on Mac `Command+Enter` or `Ctrl+Enter` on Windows.
More shortcuts you can find [here](https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts).

Changed shortcuts by me:

Windows | Mac | Action
--- | --- | ---
Ctrl-Enter | Commantd-Enter | Execute code
Ctrl-S | Command-S | Execute code
Alt-Shift-Up | Option-Shift-Up | Move lines up
Alt-Shift-Down | Option-Shift-Down | Move lines down
Ctrl-D | Command-D | Copy line
Ctrl-Shift-L | Command-Shift-L | Show sidebar
Ctrl-Shift-S | Command-Shift-S | Save Snippet
Ctrl-Shift-P | Command-Shift-P | Add Library / Package

## Changelog

- 1.4
  - docker! Now you can use Sandbox with containers and don't be afraid about lost data from computer while use dangerous functions
  - multi php now uses fastcgi to evaluate php scripts, no shel_exec anymore :)
  - small refactoring
  - new Kint for debugging
- 1.3
  - added library manager, now you are able to add external libraries from packagist, and use it in sandbox directly
- 1.2
  - fixed counting of memory used by script (now it is counting only for evaluated script without extra stuff from bootstrap)
  - added new PhpStorm shortcut
  - changed routing from FatFree to my own (FatFree fired couple ini_sets which might conflict with security settings, where  ini_set() function will be disabled)
  - added snippets, which you can save and load in any time
  - added possibility to switch between couple of php versions
- 1.1
  - added Kint debug tool for dumping varialbles
  - refactored Config class
- 1.0
  - Initial release
  - visual editor based on ace
  - simple benchmarking (count memory usage, memory peak and execution time)
  - "last load" option

## Licence

That code is licenced under [New BSD License](https://opensource.org/licenses/BSD-3-Clause).

## Credits

To build that tool I used:

- Kint - http://raveren.github.io/kint/
- Ace Editor - https://ace.c9.io/
- Bootstrap - https://getbootstrap.com/
- FontAwesome - http://fortawesome.github.io/Font-Awesome/
- jQuery - https://jquery.com/
