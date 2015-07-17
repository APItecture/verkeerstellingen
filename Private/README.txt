# ======
# LUSSEN
# ======

# 1) Lussen uit xls in excel exporteren als csv

# 2) Lussen transformeren van csv naar json in https://shancarter.github.io/mr-data-converter/

# 3) json importeren in MongoDB

  $> mongoimport --db verkeerstellingen --collection lussen --type json --file lussen.json --jsonArray
  
# =========
# TELLINGEN
# =========

# 1) Tellingen uit xls in excel exporteren als csv

# 3) csv importeren in MongoDB

  $> mongoimport --db verkeerstellingen --collection tellingen --type csv --file Data.xxxxxxx.csv --headerline

# =============
# TRANSFORMEREN
# =============

  $> mongo
  $> load("dateTransform.js")