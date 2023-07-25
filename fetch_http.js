function scrapingSite() {
	fetch('http://localhost/sitecretu/script_be_proxy.php?functie=scrape')
		.then(response => response.json())
		.then(data => {
			const elementeDeScrape = data.elementeDeScrape;
			for (var i = 0; i < elementeDeScrape.length; i++) {
				afiseazaInregistrare(i+1,elementeDeScrape[i][0], elementeDeScrape[i][1], '#tabel1');
			}
		})
		.catch(error => console.error('Eroare:', error)); 
}

function inserareSiAfisareS1() {
			
	var tableScraping = citireTabelaScraping();
	var jsonData = JSON.stringify(tableScraping);

	fetch('http://localhost/sitecretu/script_be_proxy.php?functie=inserare_afisare_s1', {
			method: 'POST',
			headers: {
			  'Content-Type': 'application/json'
			},
			body: jsonData
		})
		.then(response => response.json())
		.then(date => {  
			for (var i = 0; i < date.length; i++) {
				afiseazaInregistrare(i+1,date[i]['h1'], date[i]['h2'], '#tabel2');
			}
		})
		.catch(error => console.error('Error:', error)); 
}

function stergereSiAfisareS1() {
			
	var camp = $("#camp3").val();
		
	fetch('http://localhost/sitecretu/script_be_proxy.php?functie=stergere_afisare_s1&camp='+camp)
		.then(response => response.json())
		.then(data => {
			for (var i = 0; i < data.length; i++) {
				afiseazaInregistrare(i+1,data[i]['h1'], data[i]['h2'], '#tabel4');
			}
		})
		.catch(error => console.error('Eroare stergere:', error)); 
}


function inserareSiAfisareS2() {
	
	fetch('http://localhost/sitecretu/script_be_proxy.php?functie=inserare_afisare_s2')
		.then(response => response.json())
		.then(data => { console.log(data); })
		.catch(error => console.error('Error:', error)); 
}

function citireTabelaScraping(){
	
	var tableScraping = [];
	$("#tabel1 tr").each(function() {
	  var inserare = {};
	  $(this).find("td").each(function(index) {
		
		if(index == 1){
			inserare["h1"] = $(this).text();
		} else {
			inserare["h2"] = $(this).text();
		}
	  });
	  tableScraping.push(inserare);
	});
	var inserare = {};
	inserare["h1"] = $("#camp1").val();	
	inserare["h2"] = $("#camp2").val();
	tableScraping.push(inserare);
	return tableScraping;
}

function afiseazaInregistrare(i,camp1,camp2,tabel) {
	
	var linieTabel = $('<tr>');
	linieTabel.append('<td>'+ i +'</td>');
	linieTabel.append('<td>'+ camp1 +'</td>');
	linieTabel.append('<td>'+ camp2 +'</td>');
	$(tabel).append(linieTabel);
}
