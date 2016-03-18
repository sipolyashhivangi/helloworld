import smtplib
import sys

class smtpMail:

    __serverURL = 'localhost'

#
# sends mail to a single and/or multiple recipients using configured server
#
# to : comma separated recipients list - needs to be converted to a python list
# Now, headers should not be set with the "to" business
# this is to ensure that the recipient DOES NOT SEE the other recipients on the mail
#

    def sendmailToMany(self, sender='', to='', subject='', text=''):
        """
        Usage:
        mail('somemailserver.com', 'me@example.com', 'someone@example.com', 'test', 'This is a test')
        """
        try:
            recipientList = to.split(",")
            print recipientList
            headers = "From: %s\r\nSubject: %s\r\n\r\n" % (sender, subject)
            message = headers + text
            mailServer = smtplib.SMTP(self.__serverURL)
            mailServer.sendmail(sender, recipientList,  message)
            print sender, to, subject, message
            mailServer.quit()
        except:
            print "Unexpected Error: ", sys.exc_info()[0]
