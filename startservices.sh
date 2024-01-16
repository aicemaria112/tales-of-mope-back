#eval(service postgresql status)
service postgresql status
subScriptExitCode="$?"

if [ "$subScriptExitCode" -ne 0 ]; then
    service postgresql restart
fi
service memcached status

subScriptExitCode="$?"

if [ "$subScriptExitCode" -ne 0 ]; then
    service memcached restart
fi
