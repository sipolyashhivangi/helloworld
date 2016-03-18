#!/usr/bin/env bash

wget -O /home/ubuntu/SCRIPTS/subscription.log https://dev.flexscore.com/dev/service/api/runsubscriptionupdates?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
echo "For /dev" >> /home/ubuntu/SCRIPTS/subscription.log
date >> /home/ubuntu/SCRIPTS/subscription.log

wget -O /home/ubuntu/SCRIPTS/subscription.log https://dev.flexscore.com/test/service/api/runsubscriptionupdates?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
echo "For /test" >> /home/ubuntu/SCRIPTS/subscription.log
date >> /home/ubuntu/SCRIPTS/subscription.log

exit