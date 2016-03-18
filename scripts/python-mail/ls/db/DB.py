import MySQLdb

import sys

class DB:

    __host     = None
    
    __username = None
    
    __password = None
    
    __dbName   = None

    __dbConn   = None

    def __init__(self, host, username, password, dbName) :
        self.__host      = host
        self.__username  = username
        self.__password  = password
        self.__dbName    = dbName

    def connect(self) :
        try:
            self.__dbConn = MySQLdb.connect (host   = self.__host,
                                            user   = self.__username,
                                            passwd = self.__password,
                                            db     = self.__dbName)
        except:
            print "Unexpected error:", sys.exc_info()[0]

    def getConnection(self) :
        return self.__dbConn

    def executeSQL(self, sqlQueryString) :
        try:
            DBConnection = self.getConnection()
            DBConnection.query(sqlQueryString)
        except AttributeError, Detail:
            print "Uncexpected error:",Detail

    def getQueuedMail(self) :
        try:
            DBConnection = self.getConnection()
            sqlString = """
                           SELECT id, fromaddress, toaddress, subject, body FROM emailmaster 
                           WHERE status='Pending' and que = 0 
                           ORDER BY id asc 
                           LIMIT 1 
                        """
            sqlCursor = DBConnection.cursor()
            sqlCursor.execute(sqlString)
            mailData = sqlCursor.fetchone()
            sqlCursor.close()
            return mailData
        except:
            print "Unexpected Error: ", sys.exc_info()

    def updateQueuedMailStatus(self, mailID, status) :
        try:
            DBConnection = self.getConnection()
            print "ident %s, status %s" % (mailID, status)
            sqlString = "UPDATE emailmaster SET status = '%s' WHERE id = %s" % (status, mailID)
            DBConnection.query(sqlString)
            DBConnection.commit()
        except:
            print "Unexpected Error: ", sys.exc_info()

    def close(self) :
        try:
            self.__dbConn.close()
        except:
            print "Unexpected Error: ", sys.exc_info()         
