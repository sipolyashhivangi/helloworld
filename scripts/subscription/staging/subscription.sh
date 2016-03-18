#!/usr/bin/env bash

wget -O /home/ubuntu/SCRIPTS/subscription.log https://staging.flexscore.com/staging/service/api/runsubscriptionupdates?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
echo "For /staging" >> /home/ubuntu/SCRIPTS/subscription.log
date >> /home/ubuntu/SCRIPTS/subscription.log

wget -O /home/ubuntu/SCRIPTS/subscription.log https://staging.flexscore.com/production/service/api/runsubscriptionupdates?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
echo "For /production" >> /home/ubuntu/SCRIPTS/subscription.log
date >> /home/ubuntu/SCRIPTS/subscription.log

exit