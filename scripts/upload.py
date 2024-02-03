#!/usr/bin/env python3
from paramiko import SFTPClient, SSHClient, MissingHostKeyPolicy
from os.path import join, islink
from os import walk, environ, readlink
from stat import S_ISLNK, S_ISDIR


def rmtree(sftp: SFTPClient, path: str) -> None:
    files = sftp.listdir_attr(path)

    for file in files:
        filepath = join(path, file.filename)
        assert file.st_mode is not None
        if S_ISDIR(file.st_mode):
            if S_ISLNK(file.st_mode):
                sftp.remove(filepath)
            else:
                rmtree(sftp, filepath)
        else:
            sftp.remove(filepath)
    sftp.rmdir(path)


def upload(sftp: SFTPClient, src: str, dest: str) -> None:
    sftp.mkdir(dest)
    for dirpath, dirnames, filenames in walk(src):
        dirpath = dirpath[len(src) + 1 :]
        for dirname in dirnames:
            source = join(src, dirpath, dirname)
            target = join(dest, dirpath, dirname)
            if islink(source):
                sftp.symlink(readlink(source), target)
            else:
                sftp.mkdir(target)
        for filename in filenames:
            source = join(src, dirpath, filename)
            target = join(dest, dirpath, filename)
            if islink(source):
                sftp.symlink(readlink(source), target)
            else:
                sftp.put(join(src, dirpath, filename), join(dest, dirpath, filename))


def main():
    with SSHClient() as client:
        client.load_system_host_keys()
        client.set_missing_host_key_policy(MissingHostKeyPolicy())
        client.connect(
            "saw21.dibris.unige.it",
            username="S4943369",
            password=environ.get("SSH_PASSWORD", None),
        )
        with client.open_sftp() as sftp:
            try:
                rmtree(sftp, "public_html")
            except FileNotFoundError:
                pass
            upload(sftp, "src", "public_html")
            rmtree(sftp,join("public_html","~S4943369"))


if __name__ == "__main__":
    main()
