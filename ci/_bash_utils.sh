#!/bin/bash

build(){

#    if command_exists "mvn" && [[ "$1" != *"-"* ]] && [[ "$1" != *"SNAPSHOT"* ]] && [[ "$1" != *"2.0"* ]]; then
#        echo "Build started with MAVEN"
#        build_via_mvn $1 $2
#    el
#    if [[ "$1" == *"SNAPSHOT"* ]]; then
#        echo "Build by clone/pull github develop branch"
#        build_via_git $1 $2
#    else
#        echo "Build by download from github repository"
#        build_via_github $1 $2
#    fi

    ODB_VERSION=$1
    ODB_COMPILED_NAME="orientdb-community-${ODB_VERSION}"
    ODB_ARCHIVED_NAME="orientdb-${ODB_VERSION}"
    ODB_PACKAGE_EXT="tar.gz"
    ODB_COMPRESSED=${ODB_COMPILED_NAME}.${ODB_PACKAGE_EXT}
    OUTPUT_DIR="${2:-$(pwd)}"


    download "http://orientdb.com/download.php?email=unknown@unknown.com&file=orientdb-community-${ODB_VERSION}.tar.gz&os=linux" $OUTPUT_DIR ${ODB_COMPRESSED}

    extract "$OUTPUT_DIR/$ODB_COMPRESSED" $OUTPUT_DIR


}


command_exists () {
  type "$1" >/dev/null 2>&1 ;
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

    echo "rm -rf ${ODB_PACKAGE_PATH}"
    rm -rf ${ODB_PACKAGE_PATH}

}

download () {
    OUTPUT_DIR=$2
    PACKAGE_NAME=$3

    if [ ! -d "$OUTPUT_DIR" ]; then
        mkdir "$OUTPUT_DIR"
    fi

    if command_exists "wget" ; then
        echo "wget -q -O $OUTPUT_DIR/$PACKAGE_NAME $1"
        wget -q -O "$OUTPUT_DIR/$PACKAGE_NAME" "$1"
    elif command_exists "curl" ; then
        echo "cd ${OUTPUT_DIR}"
        cd ${OUTPUT_DIR}
        echo "curl --silent -L $1 \"$OUTPUT_DIR/$PACKAGE_NAME\""
        curl --silent -L $1 --output "$OUTPUT_DIR/$PACKAGE_NAME"
    else
        echo "Cannot download $1 [missing wget or curl]"
        exit 1
    fi
}

build_via_git (){

    ODB_VERSION=$1
    CI_DIR=$2
    ODB_ARCHIVED_NAME="orientdb-${ODB_VERSION}"

    cd ${CI_DIR}
    if [ ! -d "orientdb-develop" ]; then
        echo "No git clone found."
        echo "git clone https://github.com/orientechnologies/orientdb.git orientdb-develop"
        git clone https://github.com/orientechnologies/orientdb.git orientdb-develop
        cd orientdb-develop
        git checkout develop
    else
        echo "Git clone found. Updating."
        cd orientdb-develop
        git checkout develop
    fi

    git pull origin develop

    echo "mvn clean install -DskipTests"
    mvn clean install -DskipTests

    if [ -d "${CI_DIR}/${ODB_ARCHIVED_NAME}/distribution/target/${ODB_COMPILED_NAME}.dir" ]; then
        echo "mv \"${CI_DIR}/${ODB_ARCHIVED_NAME}\"/distribution/target/${ODB_COMPILED_NAME}.dir/* ${CI_DIR}"
        mv "${CI_DIR}/${ODB_ARCHIVED_NAME}"/distribution/target/${ODB_COMPILED_NAME}.dir/* ${CI_DIR}
    else
        echo "mv \"${CI_DIR}/${ODB_ARCHIVED_NAME}\"/distribution/target/${ODB_COMPILED_NAME}-distribution.dir/* ${CI_DIR}"
        mv "${CI_DIR}/${ODB_ARCHIVED_NAME}"/distribution/target/${ODB_COMPILED_NAME}-distribution.dir/* ${CI_DIR}
    fi

    echo "rm -rf ${CI_DIR}/releases"
    rm -rf ${CI_DIR}/releases

}

build_via_github (){

    ODB_VERSION=$1
    CI_DIR=$2
    OUTPUT_DIR="${2:-$(pwd)}"

    ODB_COMPILED_NAME="orientdb-community-${ODB_VERSION}"
    ODB_ARCHIVED_NAME="orientdb-${ODB_VERSION}"
    ODB_PACKAGE_EXT="tar.gz"
    ODB_COMPRESSED=${ODB_COMPILED_NAME}.${ODB_PACKAGE_EXT}

    GITHUB_URL="https://github.com/orientechnologies/orientdb/archive/${ODB_VERSION}.${ODB_PACKAGE_EXT}"

    download ${GITHUB_URL} ${OUTPUT_DIR} ${ODB_COMPRESSED}

    ODB_PACKAGE_PATH="${CI_DIR}/${ODB_COMPRESSED}"

    echo "Extract package"
    extract ${ODB_PACKAGE_PATH} ${CI_DIR}

    echo "cd ${CI_DIR}/${ODB_ARCHIVED_NAME}"
    cd "${CI_DIR}/${ODB_ARCHIVED_NAME}"

    echo "mvn clean install -DskipTests"
    mvn clean install -DskipTests

    if [ -d "${CI_DIR}/${ODB_ARCHIVED_NAME}/distribution/target/${ODB_COMPILED_NAME}.dir" ]; then
        echo "mv \"${CI_DIR}/${ODB_ARCHIVED_NAME}\"/distribution/target/${ODB_COMPILED_NAME}.dir/* ${CI_DIR}"
        mv "${CI_DIR}/${ODB_ARCHIVED_NAME}"/distribution/target/${ODB_COMPILED_NAME}.dir/* ${CI_DIR}
    else
        echo "mv \"${CI_DIR}/${ODB_ARCHIVED_NAME}\"/distribution/target/${ODB_COMPILED_NAME}-distribution.dir/* ${CI_DIR}"
        mv "${CI_DIR}/${ODB_ARCHIVED_NAME}"/distribution/target/${ODB_COMPILED_NAME}-distribution.dir/* ${CI_DIR}
    fi

    echo "rm -rf ${CI_DIR}/${ODB_ARCHIVED_NAME}"
    rm -rf ${CI_DIR}/${ODB_ARCHIVED_NAME}

}

#build_via_mvn () {
#
#    ODB_VERSION=$1
#    CI_DIR=$2
#
#    ODB_COMPILED_NAME="orientdb-community-${ODB_VERSION}"
#
#    ODB_PACKAGE_EXT="tar.gz"
#    ODB_COMPRESSED=${ODB_COMPILED_NAME}.${ODB_PACKAGE_EXT}
#
#    OUTPUT_DIR="${2:-$(pwd)}"
#
#    if [ ! -d "$OUTPUT_DIR" ]; then
#        mkdir "$OUTPUT_DIR"
#    fi
#
#    if command_exists "mvn" ; then
#        echo "mvn org.apache.maven.plugins:maven-dependency-plugin:2.8:get -Dartifact=com.orientechnologies:orientdb-community:\"${ODB_VERSION}\":\"${ODB_PACKAGE_EXT}\":distribution -DremoteRepositories=https://oss.sonatype.org/content/repositories/snapshots/ -Ddest=\"$OUTPUT_DIR/$ODB_COMPRESSED\""
#        mvn org.apache.maven.plugins:maven-dependency-plugin:2.8:get -Dartifact=com.orientechnologies:orientdb-community:"${ODB_VERSION}":"${ODB_PACKAGE_EXT}":distribution -DremoteRepositories=https://oss.sonatype.org/content/repositories/snapshots/ -Ddest="$OUTPUT_DIR/$ODB_COMPRESSED"
#    else
#        echo "Cannot download $1 [maven is not installed]"
#        exit 1
#    fi
#
#    ODB_PACKAGE_PATH="${CI_DIR}/${ODB_COMPILED_NAME}.${ODB_PACKAGE_EXT}"
#
#    echo "Extract package"
#    extract ${ODB_PACKAGE_PATH} ${CI_DIR}
#
#}
