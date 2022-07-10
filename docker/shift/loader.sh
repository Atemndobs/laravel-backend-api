#!/bin/sh

terminate() {
  echo
  echo 'Please send the command output and any shift.log file created'
  echo 'to support@laravelshift.com to resolve and rerun this Shift.'
  echo

  exit $1
}

echo -n 'Creating Shift environment...'
git config --global --add safe.directory /project
mkdir -p /opt/shift
cd /opt/shift
pwd
echo ' 3!'

echo -n 'Fetching resources...'
wget -qO resources.tgz https://laravelshift.com/resources.tgz

echo -n
echo -n "Current Working Directory: $PWD"
echo -n
ls -la



#if [[ $? -ne 0 ]]; then
#  echo 'Failed'
#  terminate $?
#fi

tar -xf resources.tgz
if [[ $? -ne 0 ]]; then
  echo 'Failed'
  terminate $?
fi

rm resources.tgz
git config --global core.excludesfile /opt/shift/resources/.gitignore_global
echo ' 2!'

echo -n
echo -n "Current Working Directory: $PWD \n"
echo -n
ls -la


echo -n 'Compiling Shift...'
#status=$(curl -w %{http_code} -s -X POST https://laravelshift.com/api/docker -d "token=$SHIFT_TOKEN" -d "code=$SHIFT_CODE" -o shift)
#if [[ $status -ne 200 ]]; then
#  echo "Failed ($status)"
#  terminate $status
#fi

echo ' 1!'


echo -n 'Running Shift...'
# dump output to file
php shift > /tmp/shift.log 2>&1

status="$?"

mv /tmp/shift.log /project/shift.log
#rm -rf /opt/shift

# TODO: send log to Shift
if [[ $status -ne 0 ]]; then
  echo 'Failed'
  terminate $status
fi

echo ' Finished!'

echo
echo 'Review the shift.md file for additional comments to complete the upgrade process.'
echo 'You may also review your commit history for full details of the changes Shift automated.'
echo
