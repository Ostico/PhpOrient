#!/bin/bash
set -x
set -e

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))
CI_DIR="$PARENT_DIR/ci/distributed"
DEFAULT_ORIENT_VERSION="2.1.5"

# launch simple instance in debug mode with shell hang up
while [ $# -ne 0 ]; do
  case $1 in
    -h)  #set option "a"
      HANG_UP=true
      shift
      ;;
    *) ODB_VERSION=${1:-"${DEFAULT_ORIENT_VERSION}"} ; shift ;;
    \?) #unrecognized option - show help
      echo "Usage: ./start-ci.sh [-h] [orient-version]" \\n
      exit 2
      ;;
  esac
done

if [[ -z "${ODB_VERSION}" ]]; then
    ODB_VERSION=${DEFAULT_ORIENT_VERSION}
fi

# ---- Start

ODB_DIR="${CI_DIR}/orientdb-community-${ODB_VERSION}"
ODB_LAUNCHER="${ODB_DIR}/bin/server.sh"
ODB_LAUNCHER_2="${ODB_DIR}_node_2/bin/server.sh"

echo "=== Initializing CI environment ==="

cd "$PARENT_DIR"

. "$PARENT_DIR/ci/_bash_utils.sh"

if [ ! -d "$ODB_DIR/bin" ]; then

  # Download and extract OrientDB server
  echo "--- Downloading OrientDB v${ODB_VERSION} ---"
  build ${ODB_VERSION} ${CI_DIR}

  # Ensure that launcher script is executable and copy configurations file
  echo "--- Setting up OrientDB ---"
  chmod +x ${ODB_LAUNCHER}
  chmod -R +rw "${ODB_DIR}/config/"

  echo "cp ${PARENT_DIR}/ci/distributed/orientdb-distr-hazelcast.xml \"${ODB_DIR}/config/\""
  cp ${PARENT_DIR}/ci/distributed/orientdb-distr-hazelcast.xml ${ODB_DIR}/config/

  cp ${PARENT_DIR}/ci/orientdb-server-log.properties "${ODB_DIR}/config/"

  if [ ! -d "${ODB_DIR}/databases" ]; then
    mkdir ${ODB_DIR}/databases
  fi

  cp -a ${ODB_DIR} ${ODB_DIR}_node_2

  echo "cp ${PARENT_DIR}/ci/distributed/orientdb-distr-node-1.xml \"${ODB_DIR}/config/orientdb-server-config.xml\""
  cp ${PARENT_DIR}/ci/distributed/orientdb-distr-node-1.xml ${ODB_DIR}/config/orientdb-server-config.xml

  echo "cp ${PARENT_DIR}/ci/distributed/orientdb-distr-node-2.xml \"${ODB_DIR}_node_2/config/orientdb-server-config.xml\""
  cp ${PARENT_DIR}/ci/distributed/orientdb-distr-node-2.xml ${ODB_DIR}_node_2/config/orientdb-server-config.xml

else
  echo "!!! Found OrientDB v${ODB_VERSION} in ${ODB_DIR} !!!"
fi

echo "Installing databases: "
echo "cp -a ${PARENT_DIR}/tests/default_databases/GratefulDeadConcerts \"${ODB_DIR}/databases/\""
cp -a ${PARENT_DIR}/tests/default_databases/GratefulDeadConcerts "${ODB_DIR}/databases/"

echo "cp -a ${PARENT_DIR}/tests/default_databases/VehicleHistoryGraph \"${ODB_DIR}/databases/\""
cp -a ${PARENT_DIR}/tests/default_databases/VehicleHistoryGraph "${ODB_DIR}/databases/"


# Start OrientDB in background.
echo "--- Starting 2 instance of OrientDB ---"
${ODB_LAUNCHER} </dev/null &>/dev/null &
${ODB_LAUNCHER_2} </dev/null &>/dev/null &
# Wait a bit for OrientDB to finish the initialization phase.
sleep 5
printf "\n=== The CI environment has been initialized ===\n"