#!/usr/bin/env bash

function stackHelp {
	echo ""
	echo "App tools help"
	echo ""
	echo "      help                                            Display this help information"
	echo "      build                                           Build up stack"
	echo "      rebuild                                         Bring down stack, removing volumes, and build back up"
	echo "      remove                                          Bring down stack, removing volumes"
	echo "      restart                                         Stop and start stack"
	echo "      stop                                            Stop stack"
	echo "      start                                           Start stack"
	echo "      terminal {container}                            Get an interactive terminal in a given container from the stack"
	echo ""
}

function buildStack {
	docker compose up -d --build
}

function rebuildStack {
	docker compose down -v
	docker compose up -d --build
}

function removeStack {
	docker compose down -v
}

function restartStack {
	docker compose down
  docker compose up -d
}

function stopStack {
	docker compose down
}

function startStack {
	docker compose up -d
}

function stackTerminal {
    docker container exec -it $1 bash -l
}

case $1 in

	help)
		stackHelp
		;;

	build)
		buildStack
		;;

	rebuild)
		rebuildStack
		;;

	remove)
		removeStack
		;;

	restart)
		restartStack
		;;

	stop)
		stopStack
		;;

	start)
		startStack
		;;

	terminal)
		stackTerminal "$2"
		;;

	*)
		stackHelp
		;;
esac