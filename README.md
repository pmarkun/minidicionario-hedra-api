# Api :: Minidicionario Hedra

## Pré-requisitos
* MySQL
* MongoDB
* Python
    * Flask
    * Flask-PyMongo
    * MySQLdb

## Instalando
* Clone o repositorio
* Crie um novo banco de dados MySQL
* Importe o arquivo `db/dicionario_db_latin1_2.sql.gz` para o banco MySQL
* Arrume as configurações do arquivo `settings.py-local` e salve-o como `settings.py`
* Rode o `import.py` para importar as definições do banco MySQL para o banco MongoDB
* Rode o `server.py`
