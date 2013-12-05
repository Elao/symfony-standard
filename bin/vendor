#!/bin/sh

CURRENT_DIR="$(pwd -P)"
ROOT_DIR="$(dirname ${CURRENT_DIR}/..)"
VENDORS_DIR="${ROOT_DIR}/vendor"
ARCHIVE_DIR="${ROOT_DIR}"

# Compress (no argument)
if test $# -eq 0
	then
	echo "Compress vendors into ${ARCHIVE_DIR}/vendor.tar.gz"
    $(cd ${VENDORS_DIR}; tar -zcf ${ARCHIVE_DIR}/vendor.tar.gz *)
# Extract
elif [[ $1 == "extract" ]]
    then
    echo "Removing existing vendors..."
    $(rm -rf ${VENDORS_DIR}/*)
    echo "Extract vendors into ${VENDORS_DIR} ..."
    $(cd ${VENDORS_DIR}; tar -zxf ${ARCHIVE_DIR}/vendor.tar.gz)
fi
