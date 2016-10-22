# SrtCleaner

Clean orphaned SRT files

## Usage

```
php cleaner.phar /path/to/videos
```

It will scan all the directory & sub directories looking for srt files without mathing file.

```
php cleaner.phar --lang=en,fr /path/to/videos
```

The `--lang` option let you specify that the srt files must have a lang defined (like `*.fr.srt`). 

## Building phar

Install [Box](https://box-project.github.io/box2/) :

```
curl -LSs https://box-project.github.io/box2/installer.php | php
```

Then build the phar :

```
php box.phar build
```

> In order to build the phar without the dev dependencies, it is recommended to run compose with the `--no-dev` parameter
