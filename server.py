from flask import Flask, Response
from flask.ext.pymongo import PyMongo
import json
from settings import *

app = Flask(__name__)
app.config['MONGO_HOST'] = CONFIG["mongo"]["host"]
app.config['MONGO_DBNAME'] = CONFIG["mongo"]["db"]

mongo = PyMongo(app)

@app.route('/palavra/<palavra>')
def define(palavra):
    dicionario = mongo.db[CONFIG["mongo"]["collection"]]
    resultado = dicionario.find_one_or_404({'palavra': palavra})
    resultado.pop("_id")
    resp = Response(json.dumps(resultado), status=200, mimetype='application/json')
    return resp

if __name__ == "__main__":
    app.run(debug=True)
