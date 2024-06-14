# Admin Helpers

I needed some extras for Nextcloud and so I developed this.

## additions to occ

* `occ files:exists <file> [--type=file|folder|ignore]`
* `occ files:hash <file> [--type=sha512|sha256|md5|...]`

## Usage

### FileExists command

`occ files:exists <file> [--type=file|folder|ignore]`

- checks if the given file, File ID or Nextcloud path, exists
- check if type is `--type=file` regular file
- check if type is `--type=folder` folder
- ignore type and check only existance

### FileHash command

`occ files:hash <file> [--type=sha512|sha256|md5|...]`

- output the files hash
- `--type=?` output available `hash_algos()`
