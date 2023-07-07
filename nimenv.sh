#!/bin/bash
nimpath=$HOME/.choosenim/toolchains/nim-1.6.8/bin/
_startenv () {
  if [ "$NIMENVSTATE" = START ]; then {
    echo "env already started";
  }; else {
    echo "starting env";
    
    OLDPATH=$PATH;
    PATH=$PATH:$nimpath
    
    OLDPS1=$PS1
    PS1="(nimenv) $PS1"
    NIMENVSTATE=START;
  }; fi 
}

_stopenv () {
  if [ "$NIMENVSTATE" = START ]; then {
    echo "stopping nim env";
    PATH=$OLDPATH;
    unset $OLDPATH;
    PS1=$OLDPS1;
    unset $OLDPS1;
    NIMENVSTATE=STOP;
  }; else {
    echo "nim env not running";
  }; fi 
}

_toggle_env () {
  #echo "toggling env";
  
  if [ "$NIMENVSTATE" = START ] ; then {
    _stopenv
  }; elif [ "$NIMENVSTATE" = STOP ] ; then {
    _startenv
  }; else {
    _startenv
  }; fi
}

case $1 in 
  start)
    _startenv
    ;;
  stop)
    _stopenv
    ;;
  *)
    _toggle_env
    ;;
esac

