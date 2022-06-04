# PHP CLI Helper

A simple PHP CLI helper.

## Basic Usage

You can just download the packed executable phar for this helper.

```bash
wget https://github.com/crazywhalecc/php-cli-helper/releases/download/0.1.0/cli-helper
chmod +x cli-helper
# Run it!
./cli-helper
```

## Pack Project to Single Executable File (PHP Installed required)

1. move `cli-helper` into PATH 

```bash
# If you don't want to move it, remember your download path and directly execute it with its path.
# If you are not root, remember using `sudo`.
mv ./cli-helper /usr/local/bin/cli-helper
```

2. cd to your project which will be packed, e.g. `/home/ubuntu/your-project`, and clean your dev dependencies.

```bash
cd /home/ubuntu/your-project
composer update --no-dev
```

3. RUN cli-helper, pack current directory.

> Notice: This command will ask you target phar path, entrypoint file name, etc.

```bash
cli-helper pack . app.phar
```

4. Make phar executable

```bash
# add X permission
chmod +x /tmp/app.phar

# remove phar suffix, like a binary executable
mv /tmp/app.phar /tmp/your-app-name

# You can use it any way you like, for example, move to system PATH to execute it directly
cd /tmp
./your-app-name

mv your-app-name /usr/local/bin/
your-app-name
```

## Make standalone binary executable with PHP (x86_64 only)

Prepare your phar file packed through the above steps, we assume your phar name is `your-app.phar`.

```bash
# Download micro builds from `static-php-cli` project, remember check the latest PHP version from `https://dl.zhamao.xin/php-bin/file/` !
wget https://dl.zhamao.xin/php-bin/file/micro-8.0.19-x86_64.tar.gz
tar -zxvf micro-*.tar.gz
# Combine micro and your phar files
cat ./micro your-app.phar > your-app-standalone
chmod +x your-app-standalone

# Then just execute it anywhere, even your another machine have no php environment!
./your-app-standalone
```

> Notice: This project is a complement to [static-php-cli](https://github.com/crazywhalecc/static-php-cli), for more details, check this link.
