#!/usr/bin/env bash

echo -n "For dev at " >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
date >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
result=$(wget -qO- --no-check-certificate https://dev.flexscore.com/dev/service/api/resetmontecarlofailedruns?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc)
echo "$result" >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
echo " " >> /home/ubuntu/SCRIPTS/resetmontecarlo.log

echo -n "For test at " >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
date >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
result=$(wget -qO- --no-check-certificate https://dev.flexscore.com/test/service/api/resetmontecarlofailedruns?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc)
echo "$result" >> /home/ubuntu/SCRIPTS/resetmontecarlo.log
echo " " >> /home/ubuntu/SCRIPTS/resetmontecarlo.log