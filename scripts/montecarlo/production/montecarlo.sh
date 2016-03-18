#!/usr/bin/env bash

while :
    do
        wget -O /home/ubuntu/SCRIPTS/montecarlo.log https://www.flexscore.com/service/api/runmontecarlo?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
        echo "For /production " >> /home/ubuntu/SCRIPTS/montecarlo.log
        date >> /home/ubuntu/SCRIPTS/montecarlo.log
    sleep 1
done