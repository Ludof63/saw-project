#!/usr/bin/env python3
from argparse import ArgumentParser
from tempfile import NamedTemporaryFile
from subprocess import run
from sys import executable
from os.path import dirname, join


def main():
    parser = ArgumentParser()
    parser.add_argument("-type", "-t", choices=["file", "sql"], default="sql")
    parser.add_argument("src")
    args = parser.parse_args()
    if args.type == "file":
        with open(args.src) as file:
            src = file.read()
    else:
        src = args.src
    with open(join(dirname(__file__), "php", "sql.php")) as file:
        php = file.read()
    src = '","'.join(query.strip() for query in src.split(";") if query.strip())
    src = php % (src,)
    with NamedTemporaryFile("w", delete=False) as file:
        file.write(src)
    run([executable, join(dirname(__file__), "execute.py"), file.name], check=True)


if __name__ == "__main__":
    main()
