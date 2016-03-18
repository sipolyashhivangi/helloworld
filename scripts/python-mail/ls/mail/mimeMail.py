import smtplib
import sys
import MimeWriter
import mimetools
import cStringIO

class mimeMail:

    __serverURL = 'localhost'

    def sendHtmlMail(self, sender='', to='', subject='', html=''):
        try:
	    recipientList = to.split(",")
            out = cStringIO.StringIO() # output buffer for our message
            htmlin = cStringIO.StringIO(html)
            writer = MimeWriter.MimeWriter(out)
            writer.addheader("Subject", subject)
            writer.addheader("From", "%s"%sender)
            writer.addheader("MIME-Version", "1.0")
            writer.startmultipartbody("alternative")
            writer.flushheaders()
            subpart = writer.nextpart()
            subpart.addheader("Content-Transfer-Encoding", "quoted-printable")
            pout = subpart.startbody("text/html", [("charset", 'us-ascii')])
            mimetools.encode(htmlin, pout, 'quoted-printable')
            htmlin.close()
            writer.lastpart()
            msg = out.getvalue()
            out.close()
            server = smtplib.SMTP(self.__serverURL)
	    print sender
            server.sendmail(sender, recipientList, msg)
            server.quit()
        except:
            print "Unexpected Error: ", sys.exc_info()[0]
