 ##
 # Python Script for emailer
 # 
 # @author Subramanya HS /Ganesh Manoharan(For TruGlobal Inc)
 # 
 # @copyright (c) 2012-2013
 ##
#!/usr/bin/python
from ls.db.DB import DB
from ls.mail.mimeMail import mimeMail
import time

def getqueuedMailandSend() :

    myDB = DB("localhost", "leapdbadmin", "k$REW;l", "leapscoremaster")    
    xsmtpMail = mimeMail()
    recipients = None
    myDB.connect()
    while 1:
        mail = myDB.getQueuedMail()
        if None == mail :
            print "No Queued Mails found!"
            break
        print "Queued mail found!", mail[0], mail[1], mail[2], mail[3], mail[4]
        try:
	    xsmtpMail.sendHtmlMail(mail[1], mail[2], mail[3], mail[4])
            myDB.updateQueuedMailStatus(mail[0], 'Sent')
        except:
            print "Failed to send mail to %s" % (mail[2])
            myDB.updateQueuedMailStatus(mail[0], 'Failed')
    myDB.close()

while 1 :
    getqueuedMailandSend()
    print "sleeping 30 seconds"
    time.sleep(30)
