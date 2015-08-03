# ======
# LUSSEN
# ======

# 1) Lussen uit xls in excel exporteren als csv

# 2) Lussen transformeren van csv naar json in https://shancarter.github.io/mr-data-converter/

# 3) json importeren in MongoDB

  $> mongoimport --db verkeerstellingen --collection lussen --type json --file lussen.json --jsonArray
  
# =========
# Data 2014
# =========

# Voor 2014 zijn alleen bewerkte XLS files beschikbaar. Deze moesten worden omgezet naar csv en velden moesten worden toegevoegd.
# Dit is een grote hoeveelheid werk.

# =========
# TELLINGEN
# =========

# 1) Tellingen uit xls in excel exporteren als csv

# 3) csv importeren in MongoDB

  $> mongoimport --db verkeerstellingen --collection tellingen --type csv --file Data.xxxxxxx.csv --headerline
  
# ============
# UNIEKE INDEX
# ============

  $> mongo
  $> db.tellingen.createIndex({Telpunt: 1, Richting: 1, Datum: 1, Uur: 1}, {unique: true})

# =============
# TRANSFORMEREN
# =============

  $> mongo
  $> load("dateTransform.js")