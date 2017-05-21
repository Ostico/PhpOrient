#!/bin/bash

build(){

    ODB_VERSION=$1
    ODB_COMPILED_NAME="orientdb-community-${ODB_VERSION}"
    ODB_ARCHIVED_NAME="orientdb-${ODB_VERSION}"
    ODB_PACKAGE_EXT="tar.gz"
    ODB_COMPRESSED=${ODB_COMPILED_NAME}.${ODB_PACKAGE_EXT}
    OUTPUT_DIR="${2:-$(pwd)}"

    if [[ "${ODB_VERSION}" == *"SNAPSHOT"* ]]; then
        download "https://oss.sonatype.org/service/local/artifact/maven/content?r=snapshots&g=com.orientechnologies&a=orientdb-community&v=${ODB_VERSION}&e=tar.gz" $OUTPUT_DIR ${ODB_COMPRESSED}
    else
        download "http://central.maven.org/maven2/com/orientechnologies/orientdb-community/${ODB_VERSION}/orientdb-community-${ODB_VERSION}.tar.gz" $OUTPUT_DIR ${ODB_COMPRESSED}
    fi
    extract "$OUTPUT_DIR/$ODB_COMPRESSED" $OUTPUT_DIR
    clean "$OUTPUT_DIR/$ODB_COMPRESSED"


}

command_exists () {
  type "$1" >/dev/null 2>&1 ;
}

download () {
    OUTPUT_DIR=$2
    PACKAGE_NAME=$3

    if [ ! -d "$OUTPUT_DIR" ]; then
        mkdir "$OUTPUT_DIR"
    fi

    if command_exists "wget" ; then
        echo "wget -q -O $OUTPUT_DIR/$PACKAGE_NAME $1"
#        wget -q -O "$OUTPUT_DIR/$PACKAGE_NAME" "$1"
        wget -O "$OUTPUT_DIR/$PACKAGE_NAME" "$1"
    elif command_exists "curl" ; then
        echo "cd ${OUTPUT_DIR}"
        cd ${OUTPUT_DIR}
        echo "curl --silent -L $1 \"$OUTPUT_DIR/$PACKAGE_NAME\""
#        curl --silent -L $1 --output "$OUTPUT_DIR/$PACKAGE_NAME"
        curl -L $1 --output "$OUTPUT_DIR/$PACKAGE_NAME"
    else
        echo "Cannot download $1 [missing wget or curl]"
        exit 1
    fi
}

extract(){

    ODB_PACKAGE_PATH=$1
    filename=$(basename "${ODB_PACKAGE_PATH}")
    CI_DIR=$2

    echo "Extract archive: ${filename}"
    if [ ${filename#*tar.gz} ]; then
        # empty string found, means no tar archive extension found
        echo "unzip -q ${ODB_PACKAGE_PATH} -d ${CI_DIR}"
        unzip -q ${ODB_PACKAGE_PATH} -d ${CI_DIR}
    elif [ ${filename#*zip} ]; then
        # empty string found, means no zip archive extension found
        echo "tar xf ${ODB_PACKAGE_PATH} -C ${CI_DIR}"
        tar xf ${ODB_PACKAGE_PATH} -C ${CI_DIR}
    else
        echo "Unknown file type"
        exit 1
    fi;

}

clean(){
    ODB_PACKAGE_PATH=$1
    echo "rm -rf ${ODB_PACKAGE_PATH}"
    rm -rf ${ODB_PACKAGE_PATH}
}
