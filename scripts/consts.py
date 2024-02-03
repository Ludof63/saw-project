#!/usr/bin/env python3
from os import chdir, listdir
from os.path import dirname, splitext, join
from toml import load

PHP = """<?php

declare(strict_types=1);

{}
"""

PHP_LINE = 'define("{}", {});'

JAVASCRIPT = """{}

export enum ApiType {{
{}
}}

export const POST_API = [
{}
];
"""
JAVASCRIPT_LINE = """export const {} = {};"""
JAVASCRIPT_ENUM_LINE = """    {} = "{}","""
JAVASCRIPT_ARRAY_LINE = """    ApiType.{},"""


def main():
    chdir(dirname(dirname(__file__)))
    consts = load("consts.toml")
    apis = [splitext(f)[0] for f in listdir("src/api")]
    post = [
        api
        for api in apis
        if "validate($validations, false"
        not in open(join("src/api", f"{api}.php")).read()
    ]
    with open("src/php/api_consts.php", "wt") as f:
        f.write(
            PHP.format(
                "\n".join(
                    PHP_LINE.format(key, repr(value)) for key, value in consts.items()
                )
            )
        )
    with open("src/ts/api_consts.ts", "wt") as f:
        f.write(
            JAVASCRIPT.format(
                "\n".join(
                    JAVASCRIPT_LINE.format(key, repr(value))
                    for key, value in consts.items()
                ),
                "\n".join(
                    JAVASCRIPT_ENUM_LINE.format(api.upper(), api) for api in apis
                ),
                "\n".join(JAVASCRIPT_ARRAY_LINE.format(api.upper()) for api in post),
            )
        )


if __name__ == "__main__":
    main()
