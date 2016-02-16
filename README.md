# PHP Sandbox

Simple and usefull tool to execute custom php scripts. It allows to block specified function to increase security.

## Screenshot

<img src="http://blog.lugowski.eu/wp-content/uploads/2016/02/php_sandbox.png?v=1.1" alt="PHP Sandbox" border="0" />

## Installation

Clone that repository wherever you want (in my example `/www/php_sandbox`), then in base dir run:

```
composer update --no-dev
```

Create entry in `/etc/hosts` for example:

```
sudo echo "127.0.0.1  phpsandbox.lc" >> /etc/hosts
```

Add new `vhost` configuration in your apache:

```
<VirtualHost *:80>
    DocumentRoot /www/php_sandbox/webroot
    ServerName phpsandbox.lc
    <Directory "/www/php_sandbox/webroot">
        AllowOverride All
        Order Allow,Deny
        Allow From All
    </Directory>
</VirtualHost>
```

Restart Apache, and Voila!

## Configuration

You can change blocked function list or add/change more directives for php evaluator. To do that, open `src/Config.php` 
and change settings you want.

## Usage

Just type some code and push `Evaluate` button or use keyboard shortcut on Mac `Command+Enter` or `Ctrl+Enter` on Windows.
More shortcuts you can find [here](https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts).

Changed shortcuts by me:

Windows | Mac | Action
--- | --- | ---
Alt-Shift-Up | Option-Shift-Up | Move lines up
Alt-Shift-Down | Option-Shift-Down | Move lines down
Ctrl-D | Command-D | Copy line
Ctrl-S | Command-S | Execute code

## Changelog
- 1.1
  - added Kint debug tool for dumping varialbles
  - refactored Config class
- 1.0
  - Initial release
  - visual editor based on ace
  - simple benchmarking (count memory usage, memory peak and execution time)
  - "last load" option

## Licence

That code is licenced under New BSD License.

```
Copyright (c) 2016, Paweł Ługowski <pawelugowski@gmail.com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```

## Credits

To build that tool I used:

- Kint - http://raveren.github.io/kint/
- Ace Editor - https://ace.c9.io/
- Bootstrap - https://getbootstrap.com/
- FontAwesome - http://fortawesome.github.io/Font-Awesome/
- jQuery - https://jquery.com/
