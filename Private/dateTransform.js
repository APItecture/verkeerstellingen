conn = new Mongo();
db = conn.getDB("verkeerstellingen");
cursor = db.tellingen.find().forEach(function (u) {
	d = u.Datum.split("-");
	IsoDatum = new Date(d[2] + "-" + d[1] + "-" + d[0] + " " + u.Uur);
	db.tellingen.update(u, {$set: {
		"date": IsoDatum,
		"jaar": d[2],
		"maand": d[1],
		"dag": d[0],
		"uur": (u.Uur.split(":"))[0],
		"weekdag": IsoDatum.getDay().toString(10)
	}});
});
