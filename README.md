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

## Pack Project to Single Executable File

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
cli-helper pack .
```
