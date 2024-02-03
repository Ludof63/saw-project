#!/usr/bin/env python3
from paramiko import SSHClient, MissingHostKeyPolicy
from os.path import join
from os import environ
from argparse import ArgumentParser
from httpx import get


def main():
    parser = ArgumentParser()
    parser.add_argument("file")
    parser.add_argument("--out", "-o")
    args = parser.parse_args()
    with SSHClient() as client:
        client.load_system_host_keys()
        client.set_missing_host_key_policy(MissingHostKeyPolicy())
        client.connect(
            "saw21.dibris.unige.it",
            username="S4943369",
            password=environ.get("SSH_PASSWORD", None),
        )
        with client.open_sftp() as sftp:
            sftp.put(args.file, join("public_html", "execute.php"))
            try:
                result = get("https://saw21.dibris.unige.it/~S4943369/execute.php")
                out = args.out
                print(result.text)
                if out is not None:
                    with open(out, "w") as file:
                        file.write(result.text)
            finally:
                sftp.remove(join("public_html", "execute.php"))


if __name__ == "__main__":
    main()
