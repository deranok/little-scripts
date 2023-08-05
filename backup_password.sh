#!/usr/bin/env bash
cur_d=$(pwd);
cd "/path/to/password";
gpg -c passwords
thunderbird -compose "from='oppeyjeff@gmail.com',to='oppeyjeff@gmail.com',subject='test',body='test',attachment='/home/jeff/Documents/passwords.gpg'"
cd $cur_d;
