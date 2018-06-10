## Todo list

Features/bugs/etc planned for disc-golf-stats

### Misc

* remember me -functionality (http://fishbowl.pastiche.org/2004/01/19/persistent%5Flogin%5Fcookie%5Fbest%5Fpractice/)
* reset password -functionality
* set collation (order by name not working in page player for the course list, ä before a)
* site icon
* Date-picker http://eonasdan.github.io/bootstrap-datetimepicker/
* testejä tietokannalle, malleille etc. myös testi onko cache päällä
* tuloskortin laskennalle ja esittämiselle oma model player_scores-listan sijaan

### Graphs

* pelien määrä kuukausittain -kaavio: kun painaa 'kaikki pelit', kaavio liikahtaa paikoilleen, korjaa ettei liikahda
* jonkinlainen diagrammi (käytä heatmap, kuten githubissa) pelien lopetusajasta, myös viikonpäivä mukaan(?) (vain sopiva kaavio puuttuu http://raphaeljs.com/github/dots.html http://visible.io/charts/column/punch-card.html)

### Tilastot-sivu

* longest streak (days in row)
* longest birdie streak
* (longest par streak)

### Pelit

* mobile gui: lisää validointi sille että 'sadetta' ja 'märkää_ei_sadetta' ei voi olla samaan aikaan
* mobile gui: tutki miksi virheviesti tulee välillä
* harkintaan: heitot nimen viereen, ob oikeaan laitaan, alusta par:eilla eikä nollilla, +- napit tulokselle
* pelin lisäys: pelaaja-checkboxit kun pelaajamäärä != 5

### Radat-sivu

* lisää eagle tulosten jakautuminen -kuvaan
* vaihda popularity <-> name javascriptillä ja päivitä ratalinkkien get-parametri oikeaksi
* pelaajakohtainen top5-näkymä, drop down pelaajavalinta (samaan kuin nykyinen ja default valinta "kaikki")
* rate courses with 5 stars, how overall score and user's own score
* väylien pituudet

### Pelaajat-sivu

* "ratojen tulosten keskiarvot" Kivikon tulokset väärin (skipatut väylät)

### Kisat-sivu

* päivämäärän formaatti samaksi kuin pelit-sivulla
* pisteet menevät väärin jos jollain laiton peli (ainakin jos laiton + kaikki tulokset nollia)
* kisaa tehdessä ja muokatessa valita "laske pisteet" / "käytä pistelaskua", joka määrää näytetäänkö pistetaulukko
