#!/usr/bin/env bash

while :
    do
        wget -O /home/ubuntu/SCRIPTS/montecarlo.log --no-check-certificate https://dev.flexscore.com/dev/service/api/runmontecarlo?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
        echo "For /dev" >> /home/ubuntu/SCRIPTS/montecarlo.log
        date >> /home/ubuntu/SCRIPTS/montecarlo.log

        wget -O /home/ubuntu/SCRIPTS/montecarlo.log --no-check-certificate https://dev.flexscore.com/test/service/api/runmontecarlo?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
        echo "For /test" >> /home/ubuntu/SCRIPTS/montecarlo.log
        date >> /home/ubuntu/SCRIPTS/montecarlo.log
    sleep 1
done