#!/bin/bash
set -e

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))
CI_DIR="$PARENT_DIR/ci/environment"

ODB_VERSION=${1:-"2.0-M3"}

# launch simple instance in debug mode with shell hang up
HANG_UP=""
case $2 in
    -h) HANG_UP=1;;
    *) # unknown option
    ;;
esac

ODB_DIR="${CI_DIR}/orientdb-community-${ODB_VERSION}"
ODB_LAUNCHER="${ODB_DIR}/bin/server.sh"

echo "=== Initializing CI environment ==="

cd "$PARENT_DIR"

. "$PARENT_DIR/ci/_bash_utils.sh"

if [ ! -d "$ODB_DIR" ]; then
  # Download and extract OrientDB server
  echo "--- Downloading OrientDB v${ODB_VERSION} ---"
  build ${ODB_VERSION} ${CI_DIR}

  # Ensure that launcher script is executable and copy configurations file
  echo "--- Setting up OrientDB ---"
  chmod +x ${ODB_LAUNCHER}
  chmod -R +rw "${ODB_DIR}/config/"
  cp ${PARENT_DIR}/ci/orientdb-server-config.xml "${ODB_DIR}/config/"
  cp ${PARENT_DIR}/ci/orientdb-server-log.properties "${ODB_DIR}/config/"
  mkdir ${ODB_DIR}/databases
  cp -a ${PARENT_DIR}/ci/GratefulDeadConcerts "${ODB_DIR}/databases/"
else
  echo "!!! Found OrientDB v${ODB_VERSION} in ${ODB_DIR} !!!"
fi

# Start OrientDB in background.
echo "--- Starting an instance of OrientDB ---"
if [ -z "${HANG_UP}" ]; then
    sh -c ${ODB_LAUNCHER} </dev/null &>/dev/null &
    # Wait a bit for OrientDB to finish the initialization phase.
    sleep 5
    printf "\n=== The CI environment has been initialized ===\n"
else
    sh -c ${ODB_LAUNCHER}
fi
