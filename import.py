import MySQLdb, pymongo
from settings import *

mysql_db = MySQLdb.connect(host=CONFIG["mysql"]["host"], # your host, usually localhost
                     user=CONFIG["mysql"]["user"], # your username
                      passwd=CONFIG["mysql"]["passwd"], # your password
                      db=CONFIG["mysql"]["db"]) # name of the data base

mongo_con = pymongo.MongoClient(CONFIG["mongo"]["host"], 27017)
mongo_db = mongo_con[CONFIG["mongo"]["db"]]
dicionario = mongo_db[CONFIG["mongo"]["collection"]]

cur = mysql_db.cursor()


query = """SELECT `Verbete`,`AcepcaoRevisor2` FROM `VerbetesNovo` INNER JOIN `DefinicoesNovo` ON `DefinicoesNovo`.idVerbete = `VerbetesNovo`.id INNER JOIN `AcepcoesNovo` ON `DefinicoesNovo`.id = `AcepcoesNovo`.`idDefinicao`"""
cur.execute(query)
size = int(cur.rowcount)
print "Getting total count... " + str(size)

pos = 0
step = 100
while pos < size:
    if (1==1):
        print "Loading from " + str(pos) + " to " + str(pos+step)
    query = """SELECT `Verbete`,`AcepcaoRevisor2` FROM `VerbetesNovo` INNER JOIN `DefinicoesNovo` ON `DefinicoesNovo`.idVerbete = `VerbetesNovo`.id INNER JOIN `AcepcoesNovo` ON `DefinicoesNovo`.id = `AcepcoesNovo`.`idDefinicao` LIMIT """ + str(pos) + "," + str(step)
    cur.execute(query)
    for row in cur.fetchall():
        palavra = dicionario.update({"palavra" : unicode(row[0], "latin-1")}, {"$addToSet" : { "significados" : unicode(row[1], "latin-1")}}, True)
        pos += 1
