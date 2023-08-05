#!/usr/bin/env bash
cur_d=$(pwd);
cd "/path/to/password";
gpg -c passwords
thunderbird -compose "from='you@email.com',to='you@email.com',subject='test',body='test',attachment='/path/to/password/passwords.gpg'"
cd $cur_d;
